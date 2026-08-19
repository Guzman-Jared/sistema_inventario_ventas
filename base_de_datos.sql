USE sistema_inventario;

CREATE TABLE usuarios (
    usuario_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) NOT NULL
);

CREATE TABLE categorias (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL
);

CREATE TABLE productos (
    producto_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_producto VARCHAR(150) NOT NULL,
    categoria_id INT NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    FOREIGN KEY (categoria_id) REFERENCES categorias(categoria_id) ON DELETE CASCADE ON UPDATE CASCADE
);

INSERT INTO categorias (nombre_categoria) VALUES
('Computadoras'),
('Accesorios'),
('Oficina');

enteros)
INSERT INTO productos (nombre_producto, categoria_id, stock, precio) VALUES
('Laptop Dell Inspiron 15', 1, 15, 720.00),
('Mouse Inalámbrico Logitech', 2, 25, 12.00);

SELECT * FROM productos WHERE id = 2;

UPDATE productos SET precio = 12.00 WHERE id = 2;

DELETE FROM productos WHERE id = 3;

SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio FROM productos p
INNER JOIN categorias c ON p.categoria_id = c.id;

SELECT p.id, p.nombre_producto, c.nombre_categoria, p.stock, p.precio FROM productos p
INNER JOIN categorias c ON p.categoria_id = c.id WHERE c.nombre_categoria = 'Accesorios';

SELECT COUNT(id) AS total_productos_catalogo FROM productos;

SELECT SUM(precio * stock) AS valor_monetario_inventario FROM productos;

SELECT MAX(precio) AS producto_mas_caro FROM productos;

SELECT c.nombre_categoria, SUM(p.stock) AS existencias_totales
FROM productos p
INNER JOIN categorias c ON p.categoria_id = c.id
GROUP BY c.nombre_categoria;
-- 1. Crear la tabla de proveedores
CREATE TABLE proveedores (
    proveedor_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_empresa VARCHAR(150) NOT NULL,
    contacto VARCHAR(100),
    telefono VARCHAR(20),
    direccion TEXT
);

-- 2. Insertar los dos proveedores de prueba
INSERT INTO proveedores (nombre_empresa, contacto, telefono, direccion) VALUES
('Tech Data El Salvador', 'Juan Pérez', '2255-8899', 'San Salvador, Col. Escalón'),
('Distribuidora de Papel', 'María Gómez', '2666-4433', 'San Miguel, Centro');


-- 1. Tabla Maestra de Compras (Cabecera de Factura)
CREATE TABLE compras (
id INT AUTO_INCREMENT PRIMARY KEY,
proveedor_id INT NOT NULL,
usuario_id INT NOT NULL,
fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
total DECIMAL(10, 2) NOT NULL,
FOREIGN KEY (proveedor_id) REFERENCES proveedores(id),
FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- 2. Tabla Detalle de Compras (Líneas de los productos ingresados)
CREATE TABLE detalle_compras (
id INT AUTO_INCREMENT PRIMARY KEY,
compra_id INT NOT NULL,
producto_id INT NOT NULL,
cantidad INT NOT NULL,
precio_compra DECIMAL(10, 2) NOT NULL,
FOREIGN KEY (compra_id) REFERENCES compras(id),
FOREIGN KEY (producto_id) REFERENCES productos(id)
);