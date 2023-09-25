<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\Security;

class UserService
{
    private $em;
    private $userRepository;
    private $security;

    /**
     * @param EntityManagerInterface $em
     * @param UserRepository $userRepository
     * @param Security $security
     */
    public function __construct(EntityManagerInterface $em, UserRepository $userRepository, Security $security)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->security = $security;
    }

    /**
     * @param array $parameters
     * @return User|object|null
     * @throws Exception
     */

    public function register(array $parameters)
    {
        $user = $this->userRepository->findOneBy(['email' => $parameters['email']]);
        if ($user) {
            throw new Exception('User Already Registered Via This Email');
        }
        $user = new User();
        $user->setEmail($parameters['email']);
        $user->setName($parameters['name']);
        $user->setStatus(1);
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(md5($parameters['password']));
        $this->em->persist($user);
        $this->em->flush();
        return $user;
    }

    /**
     * @throws Exception
     */
    public function login(array $parameters)
    {
        $user = $this->em->getRepository(User::class)->findOneBy(array('email' => $parameters['email']));
        if ($user === null) {
            throw new Exception('Please Register First');
        }
        if ($user->getPassword() !== md5($parameters['password'])) {
            throw new Exception('Incorrect Password,try Again!');
        }
        return $user;
    }

    public function index(array $parameters)
    {
        $defaults = [
            'pageNumber' => 1,
            'rowsPerPage' => '',
            'searchText' => '',
            'orderBy' => 'id',
            'order' => 'desc'
        ];

        $parameters = array_merge($defaults, $parameters);
        $repo = $this->em->getRepository(User::class);

        $users = $repo->createQueryBuilder('u');
        return $users->setMaxResults($parameters['rowsPerPage'])->setFirstResult($parameters['pageNumber'] - 1)
            ->orderBy('u.id', 'desc')
            ->getQuery()->getResult();
    }

    /**
     * @throws Exception
     */
    public function show($id): User
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if ($user === null) {
            throw new Exception('There is any user with this id');
        }
        return $user;
    }

    /**
     * @throws Exception
     */
    public function update(array $parameters, $id): User
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if ($user === null) {
            throw new Exception('There is any user with this id');
        }

        $user->setName($parameters['name']);
        $user->setPassword(md5($parameters['password']));
        $checkAnother = $this->userRepository->check($id, $parameters['email']);
        if ($checkAnother != null) {
            throw new Exception('Email is Already Given');
        }
        $user->setEmail($parameters['email']);
        $this->em->persist($user);
        $this->em->flush();
        return $user;

    }

    /**
     * @throws Exception
     */
    public function delete($id): bool
    {
        $user = $this->userRepository->findOneBy(['id' => $id]);
        if ($user === null || $user->getId() == $this->security->getUser()->getId()) {
            throw new Exception('Admin Can Not Delete');
        }
        $this->em->remove($user);
        $this->em->flush();
        return true;

    }


}
