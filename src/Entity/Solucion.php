<?php

namespace TDW18\PFinal\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Solucion
 *
 * @ORM\Table(name="soluciones", indexes={@ORM\Index(name="fk_idCuestion", columns={"idCuestion"})})
 * @ORM\Entity
 */
class Solucion implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="idSolucion", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSolucion;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="string", length=255, nullable=false)
     */
    private $texto;

    /**
     * @var bool
     *
     * @ORM\Column(name="correcta", type="boolean", nullable=false)
     */
    private $correcta;

    /**
     * @var Cuestion
     *
     * @ORM\ManyToOne(targetEntity="Cuestion", inversedBy="soluciones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCuestion", referencedColumnName="idCuestion", onDelete="CASCADE")
     * })
     */
    private $cuestion;

    /**
     * @var Razonamiento[] $razonamientos
     *
     * @ORM\OneToMany(
     *     targetEntity="Razonamiento",
     *     mappedBy="solucion"
     * )
     */
    private $razonamientos;

    /**
     * @var PropRazonamiento[] $propuestasRazonamiento
     *
     * @ORM\OneToMany(
     *     targetEntity="PropRazonamiento",
     *     mappedBy="solucion"
     * )
     */
    private $propuestasRazonamiento;


    /**
     * Solucion constructor.
     * @param string $texto
     * @param bool $correcta
     * @param Cuestion $cuestion
     */
    public function __construct(string $texto, bool $correcta, Cuestion $cuestion)
    {
        $this->texto = $texto;
        $this->correcta = $correcta;
        $this->cuestion = $cuestion;
        $this->razonamientos = new ArrayCollection();
        $this->propuestasRazonamiento = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getIdSolucion(): int
    {
        return $this->idSolucion;
    }

    /**
     * @return string
     */
    public function getTexto(): string
    {
        return $this->texto;
    }

    /**
     * @param string $texto
     */
    public function setTexto(string $texto): void
    {
        $this->texto = $texto;
    }

    /**
     * @return bool
     */
    public function isCorrecta(): bool
    {
        return $this->correcta;
    }

    /**
     * @param bool $correcta
     */
    public function setCorrecta(bool $correcta): void
    {
        $this->correcta = $correcta;
    }

    /**
     * @return Cuestion
     */
    public function getCuestion(): Cuestion
    {
        return $this->cuestion;
    }

    /**
     * @param Cuestion $cuestion
     */
    public function setCuestion(Cuestion $cuestion): void
    {
        $this->cuestion = $cuestion;
    }

    /**
     * @return Collection
     */
    public function getRazonamientos(): Collection
    {
        return $this->razonamientos;
    }

    /**
     * @return Collection
     */
    public function getPropRazonamiento(): Collection
    {
        return $this->propuestasRazonamiento;
    }


    public function jsonSimplificado(): array
    {
        return [
            'idSolucion' => $this->getIdSolucion(),
            'texto' => $this->getTexto(),
            'correcta' => $this->isCorrecta(),
            'razonamientos' => $this->getRazonamientos()->getValues()
        ];
    }

    public function jsonSerialize()
    {
        return [
            'idSolucion' => $this->getIdSolucion(),
            'texto' => $this->getTexto(),
            'correcta' => $this->isCorrecta(),
            'propuestasRazonamiento' => $this->getPropRazonamiento()->getValues(),
            'razonamientos' => $this->getRazonamientos()->getValues()
        ];
    }

}
