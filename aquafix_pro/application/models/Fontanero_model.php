<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fontanero_model extends CI_Model {

    private $tabla = 'fontaneros';

    public function __construct() {
        parent::__construct();
    }

    public function crear($datos) {
        $datos['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        $datos['fecha_registro'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->tabla, $datos);
    }

    public function obtener_por_id($id) {
        return $this->db->where('id_fontanero', $id)
                        ->get($this->tabla)
                        ->row();
    }

    public function obtener_por_email($email) {
        return $this->db->where('email', $email)
                        ->get($this->tabla)
                        ->row();
    }

    public function listar_todos($filtros = array()) {
        if (isset($filtros['disponible'])) {
            $this->db->where('disponible', $filtros['disponible']);
        }
        if (!empty($filtros['especialidad'])) {
            $this->db->like('especialidad', $filtros['especialidad']);
        }
        if (isset($filtros['min_rating'])) {
            $this->db->where('rating_promedio >=', $filtros['min_rating']);
        }
        return $this->db->order_by('rating_promedio', 'DESC')
                        ->get($this->tabla)
                        ->result();
    }

    public function listar_disponibles() {
        return $this->db->where('disponible', TRUE)
                        ->order_by('rating_promedio', 'DESC')
                        ->get($this->tabla)
                        ->result();
    }

    public function actualizar($id, $datos) {
        if (isset($datos['password']) && !empty($datos['password'])) {
            $datos['password'] = password_hash($datos['password'], PASSWORD_BCRYPT);
        } else {
            unset($datos['password']);
        }
        return $this->db->where('id_fontanero', $id)
                        ->update($this->tabla, $datos);
    }

    public function actualizar_disponibilidad($id, $disponible) {
        return $this->db->where('id_fontanero', $id)
                        ->update($this->tabla, array('disponible' => $disponible));
    }

    public function verificar_credenciales($email, $password) {
        $fontanero = $this->obtener_por_email($email);
        if ($fontanero && password_verify($password, $fontanero->password)) {
            return $fontanero;
        }
        return false;
    }

    public function incrementar_servicios($id) {
        return $this->db->set('servicios_completados', 'servicios_completados + 1', FALSE)
                        ->where('id_fontanero', $id)
                        ->update($this->tabla);
    }

    public function actualizar_rating($id) {
        $query = "UPDATE {$this->tabla} 
                  SET rating_promedio = (
                      SELECT COALESCE(AVG(puntuacion), 0)
                      FROM calificaciones
                      WHERE id_fontanero = ?
                  )
                  WHERE id_fontanero = ?";
        return $this->db->query($query, array($id, $id));
    }

    public function obtener_estadisticas($id_fontanero) {
        $this->db->select('
            COUNT(*) as total_solicitudes,
            SUM(CASE WHEN estado = "completada" THEN 1 ELSE 0 END) as completadas,
            SUM(CASE WHEN estado = "en_proceso" THEN 1 ELSE 0 END) as en_proceso,
            SUM(CASE WHEN estado = "asignada" THEN 1 ELSE 0 END) as asignadas
        ');
        $this->db->where('id_fontanero', $id_fontanero);
        return $this->db->get('solicitudes')->row();
    }

    public function contar_fontaneros() {
        return $this->db->count_all($this->tabla);
    }

    public function obtener_mejores($limite = 5) {
        return $this->db->where('disponible', TRUE)
                        ->order_by('rating_promedio', 'DESC')
                        ->limit($limite)
                        ->get($this->tabla)
                        ->result();
    }
}
