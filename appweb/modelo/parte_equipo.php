<?php

class parte_equipo {
    
    function add($param) {
        extract($param);
        $sql = "INSERT INTO parte values('$id_parte','$nombre' , '$descripcion')";
        $sql1 = "INSERT INTO parte_equipo values('$id_equipo_sala','$id_parte', '$secuencia' )";


        $conexion->getPDO()->exec($sql);
        $conexion->getPDO()->exec($sql1);

        echo $conexion->getEstado();
    }

    function edit($param) {
        extract($param);
        $sql1 = "UPDATE parte
                       SET  nombre = '$nombre', descripcion = '$descripcion'
                       WHERE id_parte = '$id_parte';";
        $sql = "UPDATE parte_equipo
                       SET  id_parte = '$id_parte'  
                       WHERE id_parte = '$id_parte';";
        
        
        $conexion->getPDO()->exec($sql1);
        $conexion->getPDO()->exec($sql);
        echo $conexion->getEstado();

    }

    function del($param) {
        extract($param);
        error_log(print_r($param, TRUE));
        $conexion->getPDO()->exec("DELETE FROM parte_equipo WHERE id_parte = '$id';");
        echo $conexion->getEstado();

    }

    /**
     * Procesa las filas que son enviadas a un objeto jqGrid
     * @param type $param un array asociativo con los datos que se reciben de la capa de presentación
     */
    function select($param) {
        extract($param);
        $where = $conexion->getWhere($param);
        // conserve siempre esta sintaxis para enviar filas al grid:
        $sql = "select pe.id_equipo_sala, p.id_parte , p.nombre , p.descripcion , pe.secuencia  from  parte p inner join parte_equipo pe on p.id_parte = pe.id_parte  $where";
        // crear un objeto con los datos que se envían a jqGrid para mostrar la información de la tabla
        $respuesta = $conexion->getPaginacion($sql, $rows, $page, $sidx, $sord); // $rows = filas * página

        // agregar al objeto que se envía las filas de la página requerida
        
        if (($rs = $conexion->getPDO()->query($sql))) {
            $cantidad = 999; // se pueden enviar al grid valores calculados o constantes
            $tiros_x_unidad = 2;
                    
            while ($fila = $rs->fetch(PDO::FETCH_ASSOC)) {
                $tipoEstado = UtilConexion::$tipoEstadoProduccion[$fila['estado']];  // <-- OJO, un valor calculado
                
                $respuesta['rows'][] = [
                    'id' => $fila['id_parte'], // <-- debe identificar de manera única una fila del grid, por eso se usa la PK
                    'cell' => [ // los campos que se muestra en las columnas del grid
                        $fila['id_equipo_sala'],
                        $fila['id_parte'],
                        $fila['nombre'],
                        $fila['descripcion'],
                        $fila['secuencia'],
                        
                        
                    ]
                ];
            }
        }
        $conexion->getEstado(false); // envía al log un posible mensaje de error si las cosas salen mal
        echo json_encode($respuesta);
    }

}





