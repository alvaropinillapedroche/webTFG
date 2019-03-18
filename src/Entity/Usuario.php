<?php

namespace TDW18\PFinal\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Usuario
 *
 * @ORM\Table(name="usuarios")
 * @ORM\Entity
 */
class Usuario implements \JsonSerializable
{
    /**
     * @var int
     *
     * @ORM\Column(name="idUsuario", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUsuario;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=32, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="pass", type="string", length=60, nullable=false)
     */
    private $pass;

    /**
     * @var bool
     *
     * @ORM\Column(name="esMaestro", type="boolean", nullable=false)
     */
    private $esMaestro;

    /**
     * @var bool
     *
     * @ORM\Column(name="activo", type="boolean", nullable=false)
     */
    private $activo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre", type="string", length=32, nullable=true)
     */
    private $nombre;

    /**
     * @var string|null
     *
     * @ORM\Column(name="apellidos", type="string", length=60, nullable=true)
     */
    private $apellidos;

    /**
     * @var int|null
     *
     * @ORM\Column(name="telefono", type="integer", nullable=true)
     */
    private $telefono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=60, nullable=true)
     */
    private $email;

    /**
     * @var Cuestion[] $cuestiones
     *
     * @ORM\OneToMany(
     *     targetEntity="Cuestion",
     *     mappedBy="creador"
     * )
     */
    private $cuestiones;

    /**
     * @var PropSolucion[] $propSolucion
     *
     * @ORM\OneToMany(
     *     targetEntity="PropSolucion",
     *     mappedBy="creador"
     * )
     */
    private $propSolucion;

    /**
     * @var PropRazonamiento[] $propRazonamiento
     *
     * @ORM\OneToMany(
     *     targetEntity="PropRazonamiento",
     *     mappedBy="creador"
     * )
     */
    private $propRazonamiento;



    /**
     * Usuario constructor.
     * @param string $username
     * @param string $pass
     */
    public function __construct(string $username = '', string $pass = '')
    {
        $this->username = $username;
        $this->setPassword($pass);
        $this->esMaestro = false;
        $this->activo = false;
        $this->cuestiones = new ArrayCollection();
        $this->propSolucion = new ArrayCollection();
        $this->propRazonamiento = new ArrayCollection();
    }

    /**
     * Set password
     *
     * @param string $password password
     *
     * @return Usuario
     */
    public function setPassword(string $password): Usuario
    {
        $this->pass = password_hash($password, PASSWORD_DEFAULT);
        return $this;
    }

    /**
     * Verifies that the given hash matches the user password.
     *
     * @param string $password password
     *
     * @return boolean
     */
    public function validatePassword($password)
    {
        return password_verify($password, $this->pass);
    }


    /**
     * @return int
     */
    public function getIdUsuario(): int
    {
        return $this->idUsuario;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPass(): string
    {
        return $this->pass;
    }

    /**
     * @return bool
     */
    public function isEsMaestro(): bool
    {
        return $this->esMaestro;
    }

    /**
     * @param bool $esMaestro
     */
    public function setEsMaestro(bool $esMaestro): void
    {
        $this->esMaestro = $esMaestro;
    }

    /**
     * @return bool
     */
    public function isActivo(): bool
    {
        return $this->activo;
    }

    /**
     * @param bool $activo
     */
    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
    }

    /**
     * @return null|string
     */
    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    /**
     * @param null|string $nombre
     */
    public function setNombre(?string $nombre): void
    {
        $this->nombre = $nombre;
    }

    /**
     * @return null|string
     */
    public function getApellidos(): ?string
    {
        return $this->apellidos;
    }

    /**
     * @param null|string $apellidos
     */
    public function setApellidos(?string $apellidos): void
    {
        $this->apellidos = $apellidos;
    }

    /**
     * @return int|null
     */
    public function getTelefono(): ?int
    {
        return $this->telefono;
    }

    /**
     * @param int|null $telefono
     */
    public function setTelefono(?int $telefono): void
    {
        $this->telefono = $telefono;
    }

    /**
     * @return null|string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param null|string $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return Collection
     */
    public function getCuestiones(): Collection
    {
        return $this->cuestiones;
    }

    /**
     * @return Collection
     */
    public function getPropSolucion(): Collection
    {
        return $this->propSolucion;
    }

    /**
     * @return Collection
     */
    public function getPropRazonamiento(): Collection
    {
        return $this->propRazonamiento;
    }

    public function jsonSerialize()
    {
        return [
            'usuario' => [
                'id' => $this->getIdUsuario(),
                'username' => $this->getUsername(),
                'esMaestro' => $this->isEsMaestro(),
                'activo' => $this->isActivo(),
                'nombre' => $this->getNombre(),
                'apellidos' => $this->getApellidos(),
                'telefono' => $this->getTelefono(),
                'email' => $this->getEmail()
            ]
        ];
    }

}
