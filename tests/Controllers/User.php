<?php

declare(strict_types=1);

/**
 * @license   MIT
 *
 * @author    Ilya Dashevsky
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace App\Controllers;

use OpenCore\Router\{Controller, Route, Body, ControllerResponse};

#[Controller('user')]
class User {

  public function __construct() {
    
  }

  private $users = [
    1 => ['id' => 1, 'name' => 'userA', 'active' => false, 'roles' => ['a', 'b', 'c']],
    2 => ['id' => 2, 'name' => 'userB', 'active' => true, 'roles' => ['a', 'd']],
  ];

  #[Route('GET', '')]
  public function getUsers(?string $filterKey = null, ?string $filterValue = null, ?bool $active = null) {
    $ret = $this->users;
    if ($filterKey === 'role') {
      $ret = array_filter($ret, fn($u) => in_array($filterValue, $u['roles']));
    }
    if ($active !== null) {
      $ret = array_filter($ret, fn($u) => $u['active'] === $active);
    }
    return array_values($ret);
  }

  #[Route('POST', '')]
  public function addUser(#[Body] array $user) {
    if (!isset($user['id'])) {
      return ControllerResponse::fromStatus(418);
    }
    if (isset($this->users[$user['id']])) {
      return ControllerResponse::fromStatus(409);
    }
    $this->users[$user['id']] = $user;
    return ControllerResponse::fromStatus(201)->withBody($user);
  }

  #[Route('PUT', '')]
  public function putUser(#[Body] array $user) {
    $id = $user['id'];
    $isNew = isset($this->users[$id]);
    $this->users[$id] = $user;
    return ControllerResponse::fromStatus($isNew ? 201 : 200)->withBody($user);
  }

  #[Route('GET', '{id}')]
  public function getUser(int $id) {
    if (!isset($this->users[$id])) {
      return ControllerResponse::fromStatus(404);
    }
    return $this->users[$id];
  }

  #[Route('DELETE', '{id}')]
  public function deleteUser(int $id) {
    if (!isset($this->users[$id])) {
      return ControllerResponse::fromStatus(404);
    }
    unset($this->users[$id]);
    return ControllerResponse::fromStatus(204);
  }

  #[Route('PATCH', '{id}')]
  public function editUser(int $id, #[Body] array $data) {
    if (isset($data['name'])) {
      $this->users[$id]['name'] = $data['name'];
    }
    if (isset($data['roles'])) {
      $this->users[$id]['roles'] = $data['roles'];
    }
    return $this->users[$id];
  }

  #[Route('GET', '{id}/roles')]
  public function getUserRoles(int $id) {
    return $this->users[$id]['roles'];
  }

}
