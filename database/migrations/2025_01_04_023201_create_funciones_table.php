<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateFuncionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Procedimiento para actualizar precios de venta
        DB::unprepared(<<<SQL
        CREATE PROCEDURE sp_actualizar_precio_venta()
        BEGIN
            UPDATE producto
            SET precio_venta_actual = precio_compra + (precio_compra * porcentaje_utilidad / 100);
        END;
        SQL);

        // Crear trigger para verificar stock mínimo
        DB::unprepared(<<<SQL
        CREATE TRIGGER trg_verificar_stock_minimo
        AFTER UPDATE ON producto
        FOR EACH ROW
        BEGIN
            IF NEW.stock < NEW.stock_minimo THEN
                IF NOT EXISTS (
                    SELECT 1
                    FROM log_errores
                    WHERE mensaje = CONCAT('Stock mínimo alcanzado para el producto: ', NEW.nombre_producto)
                      AND NEW.id_producto = (
                          SELECT id_producto FROM producto WHERE nombre_producto = NEW.nombre_producto LIMIT 1
                      )
                ) THEN
                    INSERT INTO log_errores (mensaje)
                    VALUES (CONCAT('Stock mínimo alcanzado para el producto: ', NEW.nombre_producto));
                END IF;
            END IF;
        END;
        SQL);

        // Crear trigger para eliminar registros cuando el stock se normaliza
        DB::unprepared(<<<SQL
        CREATE TRIGGER trg_eliminar_stock_minimo
        AFTER UPDATE ON producto
        FOR EACH ROW
        BEGIN
            IF NEW.stock >= NEW.stock_minimo THEN
                DELETE FROM log_errores
                WHERE mensaje = CONCAT('Stock mínimo alcanzado para el producto: ', NEW.nombre_producto);
            END IF;
        END;
        SQL);

        // Crear vista para alertas de stock
        DB::unprepared(<<<SQL
        CREATE VIEW VistaAlertasStock AS
        SELECT 
          P.id_producto,
          P.codigo_barra,
          P.nombre_producto,
          P.stock,
          P.stock_minimo,
          L.mensaje,
          L.fecha
        FROM producto P
        JOIN log_errores L ON L.mensaje = CONCAT('Stock mínimo alcanzado para el producto: ', P.nombre_producto);
        SQL);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la vista
        DB::unprepared('DROP VIEW IF EXISTS VistaAlertasStock;');

        // Eliminar triggers
        DB::unprepared('DROP TRIGGER IF EXISTS trg_verificar_stock_minimo;');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_eliminar_stock_minimo;');

        // Eliminar procedimiento almacenado
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_actualizar_precio_venta;');
    }
}
