<?php

namespace TDW18\PFinal\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Razonamiento
 *
 * @ORM\Table(name="razonamientos", indexes={@ORM\Index(name="fk_idSolucion", columns={"idSolucion"})})
 * @ORM\Entity
 */
class Razonamiento implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="idRazonamiento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRazonamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="texto", type="string", length=255, nullable=false)
     */
    private $texto;

    /**
     * @var bool
     *
     * @ORM\Column(name="justificado", type="boolean", nullable=false)
     */
    private $justificado;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error", type="string", length=255, nullable=true)
     */
    private $error;

    /**
     * @var Solucion
     *
     * @ORM\ManyToOne(targetEntity="Solucion", inversedBy="razonamientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSolucion", referencedColumnName="idSolucion", onDelete="CASCADE")
     * })
     */
    private $solucion;



    /**
     * Razonamiento constructor.
     * @param string $texto
     * @param bool $justificado
     * @param null|string $error
     * @param Solucion $solucion
     */
    public function __construct(string $texto, bool $justificado, ?string $error, Solucion $solucion)
    {
        $this->texto = $texto;
        $this->justificado = $justificado;
        $this->error = $error;
        $this->solucion = $solucion;
    }



    /**
     * @return int
     */
    public function getIdRazonamiento(): int
    {
        return $this->idRazonamiento;
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
    public function isJustificado(): bool
    {
        return $this->justificado;
    }

    /**
     * @param bool $justificado
     */
    public function setJustificado(bool $justificado): void
    {
        $this->justificado = $justificado;
    }

    /**
     * @return null|string
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @param null|string $error
     */
    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return Solucion
     */
    public function getSolucion(): Solucion
    {
        return $this->solucion;
    }

    /**
     * @param Solucion $solucion
     */
    public function setSolucion(Solucion $solucion): void
    {
        $this->solucion = $solucion;
    }

    public function jsonSerialize()
    {
        return [
            'idRazonamiento' => $this->getIdRazonamiento(),
            'texto' => $this->getTexto(),
            'justificado' => $this->isJustificado(),
            'error' => $this->getError()
        ];
    }
}
