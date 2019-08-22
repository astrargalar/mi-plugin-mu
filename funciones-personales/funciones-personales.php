<?php
/* 
 Plugin Name:Plugin con funciones personales 23082019
 Plugin URI: https://astrargalar.com 
 Description: Instala las funciones personalizadas más utilizadas
 Version: 1.0.0 
 Author: Paco Silva 
 Author URI: https://astrargalar.com 
 License: GPL 2+ 
 License URI: https://astrargalar.com 
 */

//Evita que un usuario malintencionado ejecute codigo php desde la barra del navegador
defined('ABSPATH') or die("Bye bye");

add_action('wp_enqueue_scripts', 'twentynineteenchild_enqueue_styles');
function twentynineteenchild_enqueue_styles()
{

    $parent_style = 'twenty-nineteen-parent-theme-styles'; // This is 'twentynineteen-parent-theme-styles' for the Twenty Nineteen theme.

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'twenty-nineteen-child-styles',
        get_stylesheet_directory_uri() . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style(
        'twenty-nineteen-servicios',
        get_stylesheet_directory_uri() . '/css/servicios.css', //CSS de la página de servicios
        array($parent_style),
        wp_get_theme()->get('Version')
    );
    wp_enqueue_style(
        'hero',
        get_stylesheet_directory_uri() . '/css/style-hero.css', //CSS de la página de portada
        array(),
        wp_get_theme()->get('Version')
    );
    // Esto es para poner animaciones wp_enqueue_style('animated', 'https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css', array('twenty-nineteen-child-styles'), '3.7.2', 'all');
    // luego ponemos clases en el elemento que queramos animar Class="rubberBand animated delay-2s"
    wp_enqueue_style('leaflet-css', 'https://unpkg.com/leaflet@1.5.1/dist/leaflet.css', array('twenty-nineteen-child-styles'), '1.5.1', 'all'); //CSS del mapa Leaflet.
    // Scripts

    wp_enqueue_script('jquery'); //Como WordPress ya la trae por defecto solo tenemos que llamarla
    wp_enqueue_script('leafle-js', 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.5.1/leaflet.js', array(), '1.5.1', true); //JavaScript del mapa Leaflet
    wp_enqueue_script('mis-scripts', get_stylesheet_directory_uri() . '/js/mis-scripts.js', array(), '1.5.1', true); //Algunos scripts que se necesita para que funcione la página.
}
function twentynineteenchild_starter_setup()
{
    // Habilitar el modo oscuro para el editor
    add_theme_support('editor-styles');
    add_theme_support('dark-editor-style');
    add_theme_support('wp-block-styles');

    add_theme_support('title-tag');
    add_image_size('blog-grande', 600, 300, true); // Hard Crop Mode
    add_image_size('medio', 470, 174, true);
    // $accent_color = get_theme_mod('primary_color_hue', 199);
    // var_dump(get_theme_mod(background_color()));
}
add_action('after_setup_theme', 'twentynineteenchild_starter_setup');

//Función para poner el aviso de copyright y desde el año que funciona la web
function crear_aviso_copyright()
{
    $todos_posts = get_posts('post_status=publish&order=ASC');
    $primer_post = $todos_posts[0];
    $primer_post_fecha = $primer_post->post_date_gmt;
    _e('Copyright &copy; ', 'twentynineteenchild');
    if (substr($primer_post_fecha, 0, 4) == date('Y')) {
        echo date('Y');
    } else {
        echo substr($primer_post_fecha, 0, 4) . "-" . date('Y');
    }
    echo ' <strong>' . get_bloginfo('name') . '</strong> ';
    _e('Todos los derechos reservados.', 'twentynineteenchild');
}
//Para añadir campos extras al perfil del usuario
function wp_campos_extra($contactmethods)
{
    // Añadimos Twitter
    $contactmethods['twitter'] = 'Twitter';
    // Añadimos Facebook
    $contactmethods['facebook'] = 'Facebook';

    return $contactmethods;
}
add_filter('user_contactmethods', 'wp_campos_extra', 10, 1);

//Soporte de dashicons para poder utilizar los iconos sin estar logeado y así aprovecho los iconos que carga WP por defecto.
add_action('wp_enqueue_scripts', 'load_dashicons_front_end');
function load_dashicons_front_end()
{
    wp_enqueue_style('dashicons');
}
//Añade imágenes destacadas en el feed RSS
function rss_post_thumbnail($content)
{
    global $post;
    if (has_post_thumbnail($post->ID)) {
        $content = '<p>' . get_the_post_thumbnail($post->ID) .
            '</p>' . get_the_content();
    }
    return $content;
}
add_filter('the_excerpt_rss', 'rss_post_thumbnail');
add_filter('the_content_feed', 'rss_post_thumbnail');

// Cambiar [...] por el botón Leer más ... en los extractos de los post
function boton_excerpt_more($more)
{
    global $post;
    // return '… <a href="' . get_permalink($post->ID) . '">' . 'Read More &raquo;' . '</a>'; Así pondría solo el texto Read mores, sin botón
    return $more . '<a href="' . esc_url(get_permalink()) . '" class="roll-button">' . esc_html__(' Leer más...', 'twentynineteenchild') . '</a>';
}
add_filter('excerpt_more', 'boton_excerpt_more');

//Poner permalink a las imagenes

add_filter('post_thumbnail_html', 'wps_post_thumbnail', 10, 3);
function wps_post_thumbnail($html, $post_id, $post_image_id)
{
    $html = '<span class="removed_link" title="' . get_permalink($post_id) . '">' . $html . '</span>';
    return $html;
}
//Registrar sidebar adicional
function miplugin_register_sidebars()
{
    register_sidebar(array(
        'name' => 'Sidebar lateral',
        'id' => 'sidebar-2',
        'descripcion' => 'Sidebar lateral',
        'class' => 'side-bar',
        'before_widget' => '<ul id="%1$s" class="%2$s"><li id="%1$s" class="%2$s">',
        'after_widget' => '</li></ul>',
        'before_title' => '<h4 class="titulodelwidget">',
        'after_title' => '</h4>'
    ));
}
add_action('widgets_init', 'miplugin_register_sidebars');
