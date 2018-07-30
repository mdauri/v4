<?php
namespace ado\service;

use ado\core\TFilter;
use ado\core\TCriteria;
use ado\core\TRepository;
use app\TApp;

use app\ado\model\UsersRecord;

final class TUserService {

    function getUser($username)
    {        
        
        $criteria = new TCriteria;
        //filtra por username
        $criteria->add(new TFilter('email', '=', $username));
        
        //instancia um repositorio para usuário
        $repository = new TRepository('poscontrolconfig','Users');
        // retorna todos os objetos que satisfem o critério
        $users = $repository->load($criteria);
        if ($users) {
            if (count($users) == 1) {
                $user = $users[0];                
            } else {
                unset($user);
            }
        }
        return $user;
        
    }
}
?>