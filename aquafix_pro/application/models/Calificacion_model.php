<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calificacion_model extends CI_Model {

    private $tabla = 'calificaciones';

    public function __construct() {
        parent::__construct();
    }

    public function crear($datos) {
        $datos['fecha_calificacion'] = date('Y-m-d H:i:s');
        return $this->db->insert($this->tabla, $datos);
    }

    public function obtener_por_id($id) {
        return $this->db->where('id_calificacion', $id)
                        ->get($this->tabla)
                        ->row();
    }

    public function obtener_por_solicitud($id_solicitud) {
        return $this->db->where('id_solicitud', $id_solicitud)
                        ->get($this->tabla)
                        ->row();
    }

    public function listar_por_fontanero($id_fontanero) {
        $this->db->select('c.*, 
                          CONCAT(cl.nombre, " ", cl.apellido) as nombre_cliente,
                          s.fecha_solicitud,
                          srv.nombre as nombre_servicio');
        $this->db->from($this->tabla . ' c');
        $this->db->join('clientes cl', 'c.id_cliente = cl.id_cliente');
        $this->db->join('solicitudes s', 'c.id_solicitud = s.id_solicitud');
        $this->db->join('servicios srv', 's.id_servicio = srv.id_servicio');
        $this->db->where('c.id_fontanero', $id_fontanero);
        $this->db->order_by('c.fecha_calificacion', 'DESC');
        return $this->db->get()->result();
    }

    public function obtener_promedio_fontanero($id_fontanero) {
        $this->db->select_avg('puntuacion');
        $this->db->where('id_fontanero', $id_fontanero);
        $result = $this->db->get($this->tabla)->row();
        return $result ? round($result->puntuacion, 2) : 0;
    }

    public function contar_calificaciones_fontanero($id_fontanero) {
        return $this->db->where('id_fontanero', $id_fontanero)
                        ->count_all_results($this->tabla);
    }

    public function obtener_mejores_comentarios($limite = 10) {
        $this->db->select('c.*, 
                          CONCAT(cl.nombre, " ", cl.apellido) as nombre_cliente,
                          CONCAT(f.nombre, " ", f.apellido) as nombre_fontanero');
        $this->db->from($this->tabla . ' c');
        $this->db->join('clientes cl', 'c.id_cliente = cl.id_cliente');
        $this->db->join('fontaneros f', 'c.id_fontanero = f.id_fontanero');
        $this->db->where('c.puntuacion >=', 4);
        $this->db->where('c.comentario IS NOT NULL');
        $this->db->where('c.comentario !=', '');
        $this->db->order_by('c.puntuacion', 'DESC');
        $this->db->order_by('c.fecha_calificacion', 'DESC');
        $this->db->limit($limite);
        return $this->db->get()->result();
    }

    public function verificar_puede_calificar($id_cliente, $id_solicitud) {
        $solicitud = $this->db->select('estado, id_cliente')
                              ->where('id_solicitud', $id_solicitud)
                              ->get('solicitudes')
                              ->row();
        
        if (!$solicitud || $solicitud->estado != 'completada' || $solicitud->id_cliente != $id_cliente) {
            return false;
        }

        $calificacion_existe = $this->db->where('id_solicitud', $id_solicitud)
                                        ->count_all_results($this->tabla);
        
        return $calificacion_existe == 0;
    }
}
