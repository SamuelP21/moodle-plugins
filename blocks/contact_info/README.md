# 📞 Bloque de Información de Contacto - Plugin para Moodle

Un plugin de bloque simple para Moodle que muestra información de contacto de la plataforma con configuración global.

![Versión Moodle](https://img.shields.io/badge/Moodle-4.4+-blue)
![Versión PHP](https://img.shields.io/badge/PHP-7.4+-purple)
![Licencia](https://img.shields.io/badge/Licencia-GPL%20v3-green)

## 🎯 Características

- 📞 **Teléfono de contacto** con icono
- 📧 **Email** con enlace mailto automático
- 📍 **Dirección física** con soporte multilínea
- ⚙️ **Configuración global** desde la administración del sitio
- 🎨 **Diseño limpio** con CSS personalizado
- 🌍 **Soporte multi-idioma** (Inglés y Español)

## 📸 Capturas de Pantalla

<div align="center">

### Vista del Bloque

<img src="screenshots/bloque_contact_info.png" alt="Bloque de Información de Contacto" width="400" style="border: 1px solid #ddd; border-radius: 8px;"/>

*Bloque mostrando información de contacto con iconos*

### Vista de Configuración

<img src="screenshots/configuracion_contact_info.png" alt="Configuración del Bloque de Información de Contacto" width="900" style="border: 1px solid #ddd; border-radius: 8px;"/>

*Configuración global del bloque con teléfono, email y dirección*

</div>

## 🚀 Instalación

### Requisitos

- Moodle 4.4 o superior
- PHP 7.4 o superior

### Pasos de Instalación

1. **Copiar el plugin:**

   ```bash
   cp -r contact_info /ruta/a/moodle/blocks/
   ```

2. **Instalar desde la interfaz:**
   - Acceder como administrador
   - Ir a: **Administración del sitio → Notificaciones**
   - Hacer clic en **Actualizar base de datos de Moodle**

3. **Configurar el bloque:**
   - Ir a: **Administración del sitio → Plugins → Bloques → Contact Information**
   - Configurar teléfono, email y dirección
   - Guardar cambios

## ⚙️ Configuración

### Configuración Global

1. Navegar a: **Administración del sitio → Plugins → Bloques → Contact Information**
2. Configurar los siguientes campos:

| Campo | Descripción | Ejemplo |
|-------|-------------|---------|
| **Phone number** | Número de contacto | `+34 900 123 456` |
| **Email address** | Email de contacto | `contacto@ejemplo.com` |
| **Physical address** | Dirección física (multilínea) | `Calle Ejemplo 123`<br>`Madrid, España` |

3. Hacer clic en **Guardar cambios**

## 📱 Uso del Bloque

### Añadir el Bloque a una Página

1. Activar el **modo de edición** en cualquier página
2. Buscar **"Añadir un bloque"** en el menú lateral
3. Seleccionar **"Contact Information"**
4. El bloque aparecerá mostrando la información configurada

### Personalizar Ubicación

- Arrastrar el bloque a diferentes regiones de la página
- Configurar visibilidad desde el menú del bloque (⚙️)

## 📁 Estructura del Plugin

```
blocks/contact_info/
├── README.md                      # Esta documentación
├── version.php                    # Metadatos del plugin
├── block_contact_info.php         # Clase principal del bloque
├── settings.php                   # Configuración global
├── styles.css                     # Estilos CSS personalizados
├── db/
│   └── access.php                 # Definición de capabilities
├── lang/
│   └── en/
│       └── block_contact_info.php # Strings en inglés
└── screenshots/                   # Capturas de pantalla
    └── bloque_contact_info.png    # Captura de pantalla del bloque
    └── configuracion_contact_info.png    # Captura de pantalla de la configuración del bloque
```

## 🛠️ Detalles Técnicos

### Arquitectura

Este bloque utiliza:

- **HTML Writer**: Generación de HTML con helpers de Moodle
- **Configuración Global**: Settings centralizados
- **CSS Personalizado**: Estilos propios del bloque
- **Multi-idioma**: Soporte completo de i18n

### Componente Principal

#### **Clase del Bloque (`block_contact_info.php`)**

```php
public function get_content() {
    // Obtener configuración global
    $phone = get_config('block_contact_info', 'phone');
    $email = get_config('block_contact_info', 'email');
    $address = get_config('block_contact_info', 'address');

    // Generar HTML con html_writer
    $html = html_writer::start_div('block-contact-info');
    
    if (!empty($phone)) {
        $html .= html_writer::start_div('contact-item phone-item');
        $html .= html_writer::tag('i', '', array('class' => 'fa fa-phone icon'));
        // ... más código
    }
    
    $this->content->text = $html;
}
```

## 🎨 Personalización

### Modificar Estilos

Editar `styles.css` para cambiar colores, tamaños, etc:

```css
.block_contact_info .contact-item .icon {
    color: #0f6cbf;  /* Color de iconos */
    font-size: 20px;
}

.block_contact_info .contact-details strong {
    color: #333;
    font-size: 14px;
}
```

### Añadir Nuevos Campos

1. **Añadir en `settings.php`:**

   ```php
   $settings->add(new admin_setting_configtext(
       'block_contact_info/horario',
       get_string('confighorario', 'block_contact_info'),
       get_string('confighorario_desc', 'block_contact_info'),
       'Lunes a Viernes: 9:00 - 18:00',
       PARAM_TEXT
   ));
   ```

2. **Actualizar strings en `lang/en/block_contact_info.php`:**

   ```php
   $string['confighorario'] = 'Horario de atención';
   $string['confighorario_desc'] = 'Horario de atención al público';
   $string['horario'] = 'Horario';
   ```

3. **Modificar `block_contact_info.php`** para mostrar el nuevo campo

## 🔐 Permisos

El bloque define dos capabilities:

| Capability | Descripción | Roles por defecto |
|------------|-------------|-------------------|
| `block/contact_info:addinstance` | Añadir bloque a páginas | Manager, Teacher |
| `block/contact_info:myaddinstance` | Añadir bloque al Dashboard | User |

## 🐛 Solución de Problemas

### El bloque no muestra información

- ✅ Verificar que la configuración está guardada en **Plugins → Bloques → Contact Information**
- ✅ Purgar cachés: **Administración → Desarrollo → Purgar todas las cachés**
- ✅ Verificar que al menos un campo tiene contenido

### Los estilos no se aplican

- ✅ Purgar cachés de Moodle
- ✅ Limpiar caché del navegador (Ctrl+F5)
- ✅ Verificar que `styles.css` existe en el directorio del bloque

### Los iconos no aparecen

- ✅ Verificar que el tema de Moodle incluye FontAwesome
- ✅ Algunos temas personalizados pueden no incluir FontAwesome por defecto

## 📚 Comparación con Otros Bloques

| Característica | `contact_info` | `contacto` |
|----------------|----------------|------------|
| Templates Mustache | ❌ | ✅ |
| HTML Writer | ✅ | ❌ |

**Tecnologías utilizadas:**

- PHP
- HTML Writer (API de Moodle)
- CSS
- FontAwesome
- API de Moodle

---

**Versión:** 1.0.0  
**Versión de Moodle:** 4.4+
