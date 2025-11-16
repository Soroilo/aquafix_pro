<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cliente_model extends CI_Model {

    private $tabla = 'clientes';

    public function __construct() {
        parent::__construct();
    }

    public function crear($datos) {
        $datos['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        $datos['fecha_registro'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->tabla, $datos);
    }

    public function obtener_por_id($id) {
        return $this->db->where('id_cliente', $id)
                        ->get($this->tabla)
                        ->row();
    }

    public function obtener_por_email($email) {
        return $this->db->where('email', $email)
                        ->get($this->tabla)
                        ->row();
    }

    public function listar_todos($filtros = array()) {
        if (!empty($filtros['activo'])) {
            $this->db->where('activo', $filtros['activo']);
        }
        return $this->db->get($this->tabla)->result();
    }

    public function actualizar($id, $datos) {
        if (isset($datos['password']) && !empty($datos['password'])) {
            $datos['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        } else {
            unset($datos['password']);
        }
        return $this->db->where('id_cliente', $id)
                        ->update($this->tabla, $datos);
    }

    public function eliminar($id) {
        return $this->db->where('id_cliente', $id)
                        ->update($this->tabla, array('activo' => FALSE));
    }

    public function verificar_credenciales($email, $password) {
        $cliente = $this->obtener_por_email($email);
        if ($cliente && password_verify($password, $cliente->password)) {
            return $cliente;
        }
        return false;
    }

    public function contar_clientes() {
        return $this->db->where('activo', TRUE)
                        ->count_all_results($this->tabla);
    }

    public function obtener_estadisticas($id_cliente) {
        $solicitudes = $this->db->where('id_cliente', $id_cliente)
                                ->get('solicitudes')
                                ->result();
        
        $estadisticas = array(
            'total_solicitudes' => count($solicitudes),
            'completadas' => 0,
            'pendientes' => 0,
            'en_proceso' => 0,
            'canceladas' => 0
        );

        foreach ($solicitudes as $solicitud) {
            if (isset($estadisticas[$solicitud->estado])) {
                $estadisticas[$solicitud->estado]++;
            }
        }

        return $estadisticas;
    }
}
