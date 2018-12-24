<?php

namespace TDW18\PFinal\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Cuestion
 *
 * @ORM\Table(name="cuestiones", indexes={@ORM\Index(name="fk_creador", columns={"idCreador"})})
 * @ORM\Entity
 */
class Cuestion implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="idCuestion", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idCuestion;

    /**
     * @var string
     *
     * @ORM\Column(name="enunciado", type="string", length=255, nullable=false)
     */
    private $enunciado;

    /**
     * @var bool
     *
     * @ORM\Column(name="disponible", type="boolean", nullable=false)
     */
    private $disponible;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="cuestiones")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCreador", referencedColumnName="idUsuario", onDelete="CASCADE")
     * })
     */
    private $creador;

    /**
     * @var Solucion[] $soluciones
     *
     * @ORM\OneToMany(
     *     targetEntity="Solucion",
     *     mappedBy="cuestion"
     * )
     */
    private $soluciones;

    /**
     * @var PropSolucion[] $propuestasSolucion
     *
     * @ORM\OneToMany(
     *     targetEntity="PropSolucion",
     *     mappedBy="cuestion"
     * )
     */
    private $propuestasSolucion;


    /**
     * Cuestion constructor.
     * @param string $enunciado
     * @param bool $disponible
     * @param Usuario $creador
     */
    public function __construct(string $enunciado, bool $disponible, Usuario $creador)
    {
        $this->enunciado = $enunciado;
        $this->disponible = $disponible;
        $this->creador = $creador;
        $this->soluciones = new ArrayCollection();
        $this->propuestasSolucion = new ArrayCollection();
    }


    /**
     * @return int
     */
    public function getIdCuestion(): int
    {
        return $this->idCuestion;
    }

    /**
     * @return string
     */
    public function getEnunciado(): string
    {
        return $this->enunciado;
    }

    /**
     * @param string $enunciado
     */
    public function setEnunciado(string $enunciado): void
    {
        $this->enunciado = $enunciado;
    }

    /**
     * @return bool
     */
    public function isDisponible(): bool
    {
        return $this->disponible;
    }

    /**
     * @param bool $disponible
     */
    public function setDisponible(bool $disponible): void
    {
        $this->disponible = $disponible;
    }

    /**
     * @return Usuario
     */
    public function getCreador(): Usuario
    {
        return $this->creador;
    }

    /**
     * @param Usuario $creador
     */
    public function setCreador(Usuario $creador): void
    {
        $this->creador = $creador;
    }

    /**
     * @return Collection
     */
    public function getSoluciones(): Collection
    {
        return $this->soluciones;
    }

    /**
     * @return Collection
     */
    public function getPropSolucion(): Collection
    {
        return $this->propuestasSolucion;
    }

    /**
     * @return array
     */
    public function jsonLigero(): array
    {
        return [
            'idCuestion' => $this->getIdCuestion(),
            'enunciado' => $this->getEnunciado(),
            'disponible' => $this->isDisponible()
        ];
    }

    /**
     * @return array
     */
    public function jsonSimplificado(): array
    {
        $soluciones = $this->getSoluciones()->map(
            function (Solucion $solucion) {
                return $solucion->jsonSimplificado();
            }
        );

        return [
            'idCuestion' => $this->getIdCuestion(),
            'usernameCreador' => $this->getCreador()->getUsername(),
            'enunciado' => $this->getEnunciado(),
            'disponible' => $this->isDisponible(),
            'soluciones' => $soluciones->getValues()
        ];
    }

    public function jsonSerialize()
    {
        return [
            'idCuestion' => $this->getIdCuestion(),
            'enunciado' => $this->getEnunciado(),
            'disponible' => $this->isDisponible(),
            'propuestasSolucion' => $this->getPropSolucion()->getValues(),
            'soluciones' => $this->getSoluciones()->getValues()
        ];
    }
}
