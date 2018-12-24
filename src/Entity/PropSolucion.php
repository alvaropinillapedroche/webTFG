<?php

namespace TDW18\PFinal\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PropSolucion
 *
 * @ORM\Table(name="propsolucion", indexes={@ORM\Index(name="fk_idCuestion", columns={"idCuestion"}), @ORM\Index(name="fk_idCreador", columns={"idCreador"})})
 * @ORM\Entity
 */
class PropSolucion implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="idPropSolucion", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPropSolucion;

    /**
     * @var string
     *
     * @ORM\Column(name="propuesta", type="string", length=255, nullable=false)
     */
    private $propuesta;

    /**
     * @var bool
     *
     * @ORM\Column(name="correcta", type="boolean", nullable=false)
     */
    private $correcta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="error", type="string", length=255, nullable=true)
     */
    private $error;

    /**
     * @var Cuestion
     *
     * @ORM\ManyToOne(targetEntity="Cuestion", inversedBy="propuestasSolucion")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCuestion", referencedColumnName="idCuestion", onDelete="CASCADE")
     * })
     */
    private $cuestion;

    /**
     * @var Usuario
     *
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="propSolucion")
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
     * PropSolucion constructor.
     * @param string $propuesta
     * @param null|string $error
     * @param Cuestion $cuestion
     * @param Usuario $creador
     */
    public function __construct(string $propuesta, Cuestion $cuestion, Usuario $creador)
    {
        $this->propuesta = $propuesta;
        $this->cuestion = $cuestion;
        $this->creador = $creador;
        $this->error = null;
        $this->correcta = true;
        $this->corregida = false;
    }

    /**
     * @return int
     */
    public function getIdPropSolucion(): int
    {
        return $this->idPropSolucion;
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
            'cuestion' => $this->getCuestion()->getEnunciado(),
            'propuesta' => $this->getPropuesta(),
            'correcta' => $this->isCorrecta(),
            'error' => $this->getError(),
            'corregida' => $this->isCorregida()
        ];
    }

    public function jsonSerialize()
    {
        return [
            'idPropSolucion' => $this->getIdPropSolucion(),
            'usernameCreador' => $this->getCreador()->getUsername(),
            'propuesta' => $this->getPropuesta(),
            'correcta' => $this->isCorrecta(),
            'error' => $this->getError(),
            'corregida' => $this->isCorregida()
        ];
    }

}
