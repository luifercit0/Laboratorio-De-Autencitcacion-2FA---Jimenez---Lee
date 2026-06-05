# Laboratorio de Autenticación 2FA — Luis Jimenez & Brian Lee

## Descripción General

Este laboratorio tiene como objetivo diseñar e implementar un sistema de autenticación web seguro y multicapa utilizando PHP bajo el paradigma de Programación Orientada a Objetos (POO) y MySQL. A través de este laboratorio se busca aplicar principios fundamentales de ciberseguridad defensiva, mitigando vulnerabilidades críticas mediante el hashing unidireccional de credenciales, control estricto de sesiones y la validación de datos para la prevención de ataques por inyección. El núcleo del laboratorio consiste en elevar los estándares de acceso tradicionales mediante la integración de un segundo factor de autenticación (2FA) basado en algoritmos TOTP y el despliegue de mecanismos distribuidos de auditoría.

---

## Estructura del Proyecto

```
login_auth/
│
├── clases/
│   ├── mod_db.PHP                # Conexión PDO y consultas preparadas (Patrón Repositorio)
│   ├── SanitizarEntrada.PHP      # Componente de limpieza de datos para prevenir XSS
│   ├── Registrousuario.php       # Lógica de negocio: hasheo e inserción de usuarios
│   └── Logger.php                # Manejador autónomo para escritura de archivos .log
│
├── vendor/                       # Dependencias autogeneradas por Composer (Sonata 2FA)
│
├── Registrese_form.php           # Interfaz de usuario para la creación de cuentas
├── procesar_registro.php         # Controlador: valida duplicados y procesa el registro
├── login.html                    # Formulario de acceso del sistema
├── verificar_login.php           # Cerebro de autenticación, sesiones y auditoría
├── panel.php                     # Zona restringida con bloque de seguridad y enrolamiento 2FA
├── configurar_2fa.php            # Genera y muestra el código QR dinámico
├── guardar_2fa.php               # Valida el token TOTP y activa el 2FA definitivo
├── logout.php                    # Destrucción total de la sesión activa
│
├── composer.json                 # Registro de dependencias del proyecto
├── composer.lock                 # Bloqueo de versiones de Composer
└── auditoria_eventos.log         # Archivo físico autogenerado de logs de auditoría
```
<img width="557" height="533" alt="image" src="https://github.com/user-attachments/assets/685eb9f2-dc2b-46af-914b-28dd8a1bf3df" />

---

## Tecnologías Utilizadas

| Tecnología | Uso |
|---|---|
| PHP (POO) | Lógica del servidor y controladores |
| MySQL | Persistencia de usuarios y auditoría |
| WampServer | Entorno local de desarrollo |
| Bcrypt (`PASSWORD_BCRYPT`) | Hashing unidireccional de contraseñas |
| TOTP / Google Authenticator | Segundo factor de autenticación |
| Composer | Gestión de dependencias |
| `sonata-project/google-authenticator` | Librería de generación y validación TOTP |
| PDO + Consultas Preparadas | Protección contra inyección SQL |

---

## Implementación Paso a Paso

### Paso 1 — Infraestructura de Datos Inicial

Se configuró en PhPmyAdmin usando la base de datos `company_info` con dos tablas principales:

**Tabla de usuarios:**
```sql
CREATE TABLE `usuarios` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `Nombre`       VARCHAR(50)  NOT NULL,
  `Apellido`     VARCHAR(50)  NOT NULL,
  `Usuario`      VARCHAR(50)  NOT NULL UNIQUE,
  `Correo`       VARCHAR(100) NOT NULL UNIQUE,
  `HashMagic`    VARCHAR(255) NOT NULL,
  `Fechasistema` DATETIME     NOT NULL
);
```

**Tabla de auditoría de accesos:**
```sql
CREATE TABLE `intentos_login` (
  `id`           INT AUTO_INCREMENT PRIMARY KEY,
  `Usuario`      VARCHAR(50)  NOT NULL,
  `IP_Acceso`    VARCHAR(45)  NOT NULL,
  `Estado`       VARCHAR(20)  NOT NULL,
  `Detalle`      VARCHAR(255) NOT NULL,
  `Fechasistema` DATETIME     NOT NULL
);
```
<img width="415" height="231" alt="Captura de pantalla 2026-06-04 194034" src="https://github.com/user-attachments/assets/a4298ac0-35e5-4023-bb1f-5b69d10daced" />

<img width="458" height="202" alt="Captura de pantalla 2026-06-04 194156" src="https://github.com/user-attachments/assets/cbdc39bf-84a7-4466-8e04-f4a93552ac45" />

<img width="1347" height="107" alt="Captura de pantalla 2026-06-04 194208" src="https://github.com/user-attachments/assets/51fec793-489f-422e-aa55-ae2d3fdff4e6" />

<img width="1230" height="313" alt="Captura de pantalla 2026-06-04 220453" src="https://github.com/user-attachments/assets/4276b0a3-b747-401e-a54a-d232772b6176" />
<img width="1227" height="240" alt="Captura de pantalla 2026-06-04 220507" src="https://github.com/user-attachments/assets/2f468205-1d73-4725-88c7-ed62f4d522fa" />


---

### Paso 2 — Autenticación Básica y Seguridad Unidireccional

- Se implementaron `Registrese_form.php` y `procesar_registro.php` para el registro de usuarios.
- Las contraseñas se transforman con **Bcrypt** (`password_hash(..., PASSWORD_BCRYPT)`), almacenándose como cadenas irreversibles en la columna `HashMagic`.
- `login.html` y `verificar_login.php` validan credenciales mediante `password_verify()`.
- Cada intento de acceso —exitoso o fallido— alimenta automáticamente la tabla `intentos_login`.
- El acceso al área restringida `panel.php` se protege con control estricto de sesiones (`$_SESSION`).
<img width="1481" height="905" alt="image" src="https://github.com/user-attachments/assets/5542746e-8452-484e-8881-ff7390d87953" />

<img width="1482" height="905" alt="image" src="https://github.com/user-attachments/assets/106a8b44-0cc3-4ee9-a9ce-d7d28c8f435a" />

<img width="1328" height="857" alt="image" src="https://github.com/user-attachments/assets/54abbfed-1e78-458b-a78d-e4b25bbbe064" />

<img width="1637" height="840" alt="image" src="https://github.com/user-attachments/assets/4a00976d-fca3-40f9-a871-11faf695c90a" />

<img width="1065" height="137" alt="image" src="https://github.com/user-attachments/assets/5d8ec3f2-8eed-40e9-8a92-bdd5d101b438" />

<img width="1636" height="562" alt="image" src="https://github.com/user-attachments/assets/505bf85f-5d11-457a-9324-35cb29f8557c" />




---

### Paso 3 — Expansión de la BD para el Segundo Factor

Para soportar el secreto compartido requerido por el protocolo TOTP, se alteró la tabla `usuarios`:

```sql
ALTER TABLE `usuarios`
ADD COLUMN `secret_2fa` VARCHAR(255) NULL AFTER `HashMagic`;
```
<img width="1226" height="42" alt="image" src="https://github.com/user-attachments/assets/af4d3c37-65c0-45f1-8a09-a0ff00aad4a7" />

---

### Paso 4 — Orquestación de Dependencias con Composer

Se instaló la librería TOTP ejecutando desde la raíz del proyecto:

```bash
cd C:\wamp64\www\login_auth
composer require sonata-project/google-authenticator
```

**Resultado del proceso:**

- `composer.json` — Registra formalmente la dependencia `sonata-project`.
- `vendor/` — Contiene la librería descargada y genera `vendor/autoload.php`, que permite instanciar las clases de Google Authenticator de forma nativa en la aplicación.

<img width="1072" height="401" alt="Captura de pantalla 2026-06-04 201453" src="https://github.com/user-attachments/assets/0e6bc5a9-e4e5-4620-9769-962f2e60e9d6" />
<img width="317" height="85" alt="image" src="https://github.com/user-attachments/assets/7abb9470-249a-4466-a1b9-20ff54e00084" />



---

### Paso 5 — Implementación de Enrolamiento y Validación 2FA

1. `configurar_2fa.php` invoca la librería externa para generar una **clave secreta única por usuario** y renderiza un **código QR dinámico** mediante Google Charts.
2. El usuario escanea el código QR con la aplicación móvil **Google Authenticator**.
3. Al introducir el token dinámico de 6 dígitos, `guardar_2fa.php` utiliza el método `checkCode()` para validar que el desfase de tiempo y el secreto coincidan.
4. Si la validación es exitosa, el secreto se persiste definitivamente en la base de datos.
<img width="1067" height="176" alt="image" src="https://github.com/user-attachments/assets/417c11a8-6ddd-4db9-8412-aeba195a6cc0" />
<img width="1636" height="341" alt="image" src="https://github.com/user-attachments/assets/289c645e-08ad-43e4-939a-935d7761cfd1" />
Se guardó el secreto:
<img width="1500" height="80" alt="image" src="https://github.com/user-attachments/assets/c12ad175-c168-4a3e-9c25-fca306a4b3f6" />


  
Pantallas:
Aqui verificamos el codigo en Google Authenticator:
<img width="1000" height="900" alt="Captura de pantalla 2026-06-04 213619" src="https://github.com/user-attachments/assets/6dacfa53-855a-49a2-9f62-f3bd46fee3cf" />
Con el codigo desde la app:
<img width="1200" height="1065" alt="image" src="https://github.com/user-attachments/assets/ffcd4e3b-3530-45a8-b44d-2df81e0f4a62" />
Aqui se nos confirma desde el navegador:
<img width="1000" height="400" alt="Captura de pantalla 2026-06-04 213824" src="https://github.com/user-attachments/assets/8105ba4f-a07d-4a26-baa6-cf02025a362f" />
Y listo, cuenta con 2FA:
<img width="1000" height="900" alt="Captura de pantalla 2026-06-04 213832" src="https://github.com/user-attachments/assets/5a570cf8-5497-4161-8cd6-0a575e3da4d9" />

---

### Paso 6 — Prevención de repeticiones de cuentas, correos, usuarios, etc.

**Prevención de colisiones:**
`procesar_registro.php` fue modificado para verificar, mediante consultas preparadas SQL, que ni el nombre de usuario ni el correo existan previamente, evitando registros duplicados.

<img width="587" height="202" alt="image" src="https://github.com/user-attachments/assets/f263535f-df9e-4e89-8e24-af59df95c363" />


<img width="1067" height="176" alt="image" src="https://github.com/user-attachments/assets/2ea1bd02-fa8e-40f7-837b-03d0577c1ecf" />

---

---

### Conclusiones
---

A través de este mini proyecto se comprendió la complejidad técnica de implementar la seguridad en dos pasos bajo un enfoque de defensa en profundidad. Se aprendió a mitigar riesgos de inyección SQL y exposición de credenciales mediante el uso de consultas preparadas (PDO) y hashing con Bcrypt. Asimismo, la integración del algoritmo TOTP con Google Authenticator demostró cómo un segundo factor neutraliza accesos no autorizados incluso si la contraseña es interceptada. Finalmente, el desarrollo de una auditoría distribuida entre la base de datos y un archivo .log físico independiente evidenció la importancia de garantizar la trazabilidad completa para detectar oportunamente patrones de ataque.

---

## Infromación de los estudiantes

| Campo | Información |
|-------|-------------|
| Nombre | Luis Jiménez (8-1018-1285)|
| Nombre | Brian Lee (8-1031-2047)|
| Curso | Desarrollo de Software 7 |
| Fecha de Ejecución del Laboratorio | 03-06-26 |
| Instructor del Laboratorio | Irina Fong |
