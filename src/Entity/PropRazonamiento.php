<?php

namespace TDW18\PFinal\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PropRazonamiento
 *
 * @ORM\Table(name="proprazonamiento", indexes={@ORM\Index(name="fk_idSolucion", columns={"idSolucion"}), @ORM\Index(name="fk_idCreador", columns={"idCreador"})})
 * @ORM\Entity
 */
class PropRazonamiento implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPropRazonamiento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPropRazonamiento;

    /**
     * @var string
     *
     * @ORM\Column(name="propuesta", type="string", length=255, nullable=false)
     */
    private $propuesta;

    /**
     * @var bool
     *
     * @ORM\Column(name="justificada", type="boolean", nullable=false)
     */
    private $justificada;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error", type="string", length=255, nullable=true)
     */
    private $error;

    /**
     * @var Solucion
     *
     * @ORM\ManyToOne(targetEntity="Solucion", inversedBy="propuestasRazonamiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSolucion", referencedColumnName="idSolucion", onDelete="CASCADE")
     * })
     */
    private $solucion;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="propRazonamiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCreador", referencedColumnName="idUsuario", onDelete="CASCADE")
     * })
     */
    private $creador;

    /**
     * @var bool
     *
     * @ORM\Column(name="corregida", type="boolean", nullable=false)
     */
    private $corregida;


    /**
     * PropRazonamiento constructor.
     * @param string $propuesta
     * @param Solucion $solucion
     * @param Usuario $creador
     */
    public function __construct(string $propuesta, Solucion $solucion, Usuario $creador)
    {
        $this->propuesta = $propuesta;
        $this->solucion = $solucion;
        $this->creador = $creador;
        $this->justificada = true;
        $this->error = null;
        $this->corregida = false;
    }

    /**
     * @return int
     */
    public function getIdPropRazonamiento(): int
    {
        return $this->idPropRazonamiento;
    }

    /**
     * @return string
     */
    public function getPropuesta(): string
    {
        return $this->propuesta;
    }

    /**
     * @param string $propuesta
     */
    public function setPropuesta(string $propuesta): void
    {
        $this->propuesta = $propuesta;
    }

    /**
     * @return bool
     */
    public function isJustificada(): bool
    {
        return $this->justificada;
    }

    /**
     * @param bool $justificada
     */
    public function setJustificada(bool $justificada): void
    {
        $this->justificada = $justificada;
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
     * @return bool
     */
    public function isCorregida(): bool
    {
        return $this->corregida;
    }

    /**
     * @param bool $corregida
     */
    public function setCorregida(bool $corregida): void
    {
        $this->corregida = $corregida;
    }


    public function jsonGetPropuestas()
    {
        return [
            'cuestion' => $this->getSolucion()->getCuestion()->getEnunciado(),
            'solucion' => $this->getSolucion()->getTexto(),
            'propuesta' => $this->getPropuesta(),
            'justificada' => $this->isJustificada(),
            'error' => $this->getError(),
            'corregida' => $this->isCorregida()
        ];
    }

    public function jsonSerialize()
    {
        return [
            'idPropRazonamiento' => $this->getIdPropRazonamiento(),
            'usernameCreador' => $this->getCreador()->getUsername(),
            'propuesta' => $this->getPropuesta(),
            'justificada' => $this->isJustificada(),
            'error' => $this->getError(),
            'corregida' => $this->isCorregida()
        ];
    }

}
