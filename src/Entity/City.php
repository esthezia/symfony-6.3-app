<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table(name: 'cities')]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $id_country = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\OneToMany(targetEntity: MusicBand::class, mappedBy: 'city')]
    private $musicBands;

    public function __construct()
    {
        $this->musicBands = new ArrayCollection();
    }

    /**
     * @return Collection<int, MusicBand>
     */
    public function getMusicBands(): Collection
    {
        return $this->musicBands;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCountry(): ?int
    {
        return $this->id_country;
    }

    public function setIdCountry(int $id_country): static
    {
        $this->id_country = $id_country;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Remove accents
     */
    public static function normalizeString (string $name = '') {
        if (empty($name)) {
            return '';
        }

        $result = $name;

        $replacements = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A',
            'þ' => 'b',
            'Þ' => 'B',
            'ç' => 'c', 'ć' => 'c', 'č' => 'c',
            'Ç' => 'C', 'Ć' => 'C', 'Č' => 'C',
            'đ' => 'dj',
            'Đ' => 'Dj',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'ñ' => 'n',
            'Ñ' => 'N',
            'ð' => 'o', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
            'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O',
            'ŕ' => 'r',
            'Ŕ' => 'R',
            'š' => 's',
            'Š' => 'S',
            'ß' => 'Ss',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'ý' => 'y', 'ÿ' => 'y',
            'Ý' => 'Y', 'Ÿ' => 'Y',
            'ž' => 'z',
            'Ž' => 'Z'
        ];

        $result = strtr($name, $replacements);

        return $result;
    }

    /**
     * Trim non-printable UTF-8 characters as well.
     */
    public static function mbTrim (mixed $string = null) {
        return preg_replace('/^\s+|\s+$/u', '', $string);
    }
}
