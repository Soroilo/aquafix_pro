<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Servicio_model extends CI_Model {

    private $tabla = 'servicios';

    public function __construct() {
        parent::__construct();
    }

    public function crear($datos) {
        return $this->db->insert($this->tabla, $datos);
    }

    public function obtener_por_id($id) {
        return $this->db->where('id_servicio', $id)
                        ->get($this->tabla)
                        ->row();
    }

    public function listar_todos($disponible = null) {
        if ($disponible !== null) {
            $this->db->where('disponible', $disponible);
        }
        return $this->db->order_by('tipo', 'ASC')
                        ->get($this->tabla)
                        ->result();
    }

    public function listar_por_tipo($tipo) {
        return $this->db->where('tipo', $tipo)
                        ->where('disponible', TRUE)
                        ->get($this->tabla)
                        ->result();
    }

    public function actualizar($id, $datos) {
        return $this->db->where('id_servicio', $id)
                        ->update($this->tabla, $datos);
    }

    public function eliminar($id) {
        return $this->db->where('id_servicio', $id)
                        ->update($this->tabla, array('disponible' => FALSE));
    }

    public function contar_servicios() {
        return $this->db->where('disponible', TRUE)
                        ->count_all_results($this->tabla);
    }

    public function obtener_tipos() {
        return $this->db->select('tipo')
                        ->distinct()
                        ->get($this->tabla)
                        ->result();
    }

    public function buscar($termino) {
        return $this->db->like('nombre', $termino)
                        ->or_like('descripcion', $termino)
                        ->where('disponible', TRUE)
                        ->get($this->tabla)
                        ->result();
    }

    public function obtener_populares($limite = 5) {
        $this->db->select('s.*, COUNT(sol.id_solicitud) as total_solicitudes');
        $this->db->from($this->tabla . ' s');
        $this->db->join('solicitudes sol', 's.id_servicio = sol.id_servicio', 'left');
        $this->db->where('s.disponible', TRUE);
        $this->db->group_by('s.id_servicio');
        $this->db->order_by('total_solicitudes', 'DESC');
        $this->db->limit($limite);
        return $this->db->get()->result();
    }
}
