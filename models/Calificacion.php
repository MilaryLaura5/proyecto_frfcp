<?php
// models/Calificacion.php
class Calificacion
{
    public static function guardar($id_participacion, $id_jurado, $id_concurso, $datos, $descalificado = 0)
    {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // Verificar si ya existe
            $check = $pdo->prepare("SELECT id_calificacion FROM Calificacion WHERE id_participacion = ? AND id_jurado = ?");
            $check->execute([$id_participacion, $id_jurado]);
            $existe = $check->fetch();

            // Campos de criterios
            $campos = '';
            $valores = [];
            foreach ($datos as $key => $value) {
                $campos .= "$key = ?, ";
                $valores[] = $value;
            }

            $valores[] = $descalificado;
            $valores[] = $id_participacion;
            $valores[] = $id_jurado;

            if ($existe) {
                // Actualizar
                $sql = "UPDATE Calificacion SET $campos descalificado = ? WHERE id_participacion = ? AND id_jurado = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute($valores);
            } else {
                // Insertar
                $campos = rtrim($campos, ', ') . ", id_participacion, id_jurado, id_concurso";
                $placeholders = str_repeat('?, ', count($valores)) . '?, ?, ?';
                $sql = "INSERT INTO Calificacion SET $campos";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(array_merge($valores, [$id_participacion, $id_jurado, $id_concurso]));
            }

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollback();
            error_log("Error en Calificacion::guardar: " . $e->getMessage());
            return false;
        }
    }
}
