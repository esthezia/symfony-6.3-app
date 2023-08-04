<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\MusicBand;
use App\Entity\City;
use App\Form\MusicBandFormType;

class IndexController extends AbstractController {

    public function index (Request $request, Connection $connection, EntityManagerInterface $entityManager) : Response {
        $countries = $connection->fetchAllKeyValue('SELECT id, name FROM countries');

        if ($request->isMethod('post') && !empty($request->files->get('file'))) {
            $file = $request->files->get('file');

            $filePath = $file->getRealPath();

            // allow a maximum file size of 2MB,
            // and only *.xlsx files.
            if (!$this->isValidUploadedFile($_FILES['file'], 2097152, ['xlsx'])) {
                $this->addFlash('error', 'There was an error with the file upload. Please check that it meets all the requirements, and try again. If the error persists, contact us.');
                return $this->redirectToRoute('homepage');
            }

            $excel = IOFactory::load($filePath);
            $data = $excel->getActiveSheet()->toArray(null, true, true, true);

            if (!empty($data) && (count($data) > 1)) { // the first line contains the headers
                $countriesLower = array_map(function ($v) { return strtolower(City::normalizeString($v)); }, $countries); // for safer and better comparisons
                $cities = $connection->fetchAllAssociative('SELECT * FROM cities');

                if (!empty($cities)) {
                    // format the cities in a way that is useful to us
                    $citiesFormatted = [];

                    foreach ($cities as $k => $v) {
                        $citiesFormatted[$v['id_country'] . '-' . strtolower($v['name'])] = $v['id'];
                    }
                }

                // normally, we would have some kind of mapping between the column headers
                // in the excel file received from the client, and our column names in the database;
                // OR, we would require an exact format of the excel file (number and order of columns).
                // BUT, for the sake of simplicity, for this test only, I mapped them by eye.

                foreach ($data as $i => $v) {
                    if ($i === 1) {
                        // it's 1-indexed
                        continue;
                    }

                    $band = new MusicBand;
                    $band->setName(City::mbTrim($v['A']));

                    $v['B'] = City::mbTrim($v['B']);
                    $v['C'] = City::mbTrim($v['C']);

                    if ($idCountry = (int) array_search(strtolower($v['B']), $countriesLower)) {
                    } elseif ($idCountry = (int) array_search(strtolower(City::normalizeString($v['B'])), $countriesLower)) {
                    }

                    if (!empty($citiesFormatted[$idCountry . '-' . strtolower($v['C'])])) {
                        $idCity = $citiesFormatted[$idCountry . '-' . strtolower($v['C'])];
                    } elseif (!empty($citiesFormatted[$idCountry . '-' . strtolower(City::normalizeString($v['C']))])) {
                        $idCity = $citiesFormatted[$idCountry . '-' . strtolower(City::normalizeString($v['C']))];
                    } else {
                        // save the new city in the database,
                        // using prepared statements
                        $connection->executeQuery(
                            'INSERT INTO cities(`id_country`, `name`) VALUES (' . $idCountry . ', :name)',
                            ['name' => ucwords($v['C'])],
                            ['name' => 'string']
                        );

                        $idCity = $connection->lastInsertId();

                        // cache the result
                        $citiesFormatted[$idCountry . '-' . strtolower($v['C'])] = $idCity;
                    }

                    $band->setCity($entityManager->getRepository(City::class)->find($idCity));

                    $band->setStartYear((int) $v['D']);

                    if (!empty($v['E'])) {
                        $band->setEndYear((int) $v['E']);
                    }

                    if (!empty($v['F'])) {
                        $band->setFounder(City::mbTrim($v['F']));
                    }

                    if (!empty($v['G'])) {
                        $band->setMembers((int) $v['G']);
                    }

                    if (!empty($v['H'])) {
                        $band->setGenre(City::mbTrim($v['H']));
                    }

                    if (!empty($v['I'])) {
                        $band->setDescription(City::mbTrim($v['I']));
                    }

                    $entityManager->persist($band);
                    $entityManager->flush(); // save the record in the database
                }
            }

            // prevent the user from hitting refresh, and submitting the data twice.
            return $this->redirectToRoute('homepage');
        }

        $bands = $connection->fetchAllAssociative('SELECT b.*, c.id_country FROM music_bands as b LEFT JOIN cities as c ON b.id_city = c.id;');

        return $this->render('home.html.twig', [
            'countries' => $countries,
            'cities' => $connection->fetchAllKeyValue('SELECT id, name FROM cities'),
            'bands' => $bands
        ]);
    }

    public function edit (Request $request, Connection $connection, EntityManagerInterface $entityManager, int $id) : Response {
        $band = $entityManager->getRepository(MusicBand::class)->find($id);

        $form = $this->createForm(MusicBandFormType::class, $band, [
            'countries' => $connection->fetchAllKeyValue('SELECT id, name FROM countries'),
            'attr' => [
                'autocomplete' => 'off'
            ]
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $city = $data->getCity();
            $newCity = $entityManager->getRepository(City::class)->findOneByNameAndCountry($city->getName(), $city->getIdCountry());

            if (empty($newCity)) {
                // new city to be added
                $newCity = new City;
                $newCity->setIdCountry($city->getIdCountry());
                $newCity->setName($city->getName());

                $entityManager->persist($newCity);
            }

            $band->setCity($newCity);

            $entityManager->persist($band);
            $entityManager->flush();

            $this->addFlash('notice', 'The group was saved successfully!');
            return $this->redirectToRoute('homepage');
        }

        return $this->render('edit.html.twig', [
            'form' => $form,
            'band' => $band
        ]);
    }

    public function delete (Request $request, Connection $connection, EntityManagerInterface $entityManager, int $id) : Response {
        $csrfToken = $request->request->get('token');

        if ($this->isCsrfTokenValid('delete-music-band', $csrfToken)) {
            $band = $entityManager->getRepository(MusicBand::class)->find($id);

            $entityManager->remove($band);
            $entityManager->flush();

            $this->addFlash('notice', 'The group ' . $band->getName() . ' was deleted successfully!');
            return $this->redirectToRoute('homepage');
        }

        $this->addFlash('error', 'There was an error, please try again. If the error persists, contact us.');
        return $this->redirectToRoute('homepage');
    }

    /**
     * Validates a freshly uploaded file
     *
     * @param (resource) $file - the uploaded file array (e.g. $_FILES['file'])
     * @param (int) $maxFileSize - the maximum file size allowed, in bytes. defaults to 5 MB.
     * @param (array) $fileExtAllowed - array with the file extensions allowed (e.g. ['csv', 'xls'])
     * @param (array) $fileMimeTypesAllowed - array with the allowed file mime types
     * @return (boolean)
     */
    private function isValidUploadedFile ($file = null, int $maxFileSize = 5242880, array $fileExtAllowed = [], array $fileMimeTypesAllowed = []) : bool {

        if (is_null($file)) {
            return false;
        }

        if (
            ($file['error'] > 0) ||
            (strlen($file['name']) < 1) ||
            ((int) $file['size'] > $maxFileSize) ||
            !is_uploaded_file($file['tmp_name']) ||
            (count($fileMimeTypesAllowed) && !in_array($file['type'], $fileMimeTypesAllowed)) ||
            (count($fileExtAllowed) && !in_array(substr($file['name'], strrpos($file['name'], '.') + 1), $fileExtAllowed))
        ) {
            unlink($file['tmp_name']);
            return false;
        }

        return true;
    }

}
