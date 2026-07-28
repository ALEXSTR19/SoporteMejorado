<?php
function generarPaginacion($pagina, $totalPaginas) {
    // Asegurarse de que la página actual esté dentro del rango válido
    if ($pagina < 1) $pagina = 1;
    if ($pagina > $totalPaginas) $pagina = $totalPaginas;
    
    // Generar el HTML para los controles de paginación
    $html = '<nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">';

    // Mostrar el botón de "Primera" solo si hay más de 10 páginas y la página actual no es la primera
    if ($totalPaginas > 10 && $pagina > 1) {
        $html .= '<li class="page-item">
                    <button class="page-link" onclick="cargarCiudadanos(1)">Primera</button>
                  </li>';
    }

    // Botón de "Anterior"
    if ($pagina > 1) {
        $html .= '<li class="page-item">
                    <button class="page-link" onclick="cargarCiudadanos(' . ($pagina - 1) . ')">Anterior</button>
                  </li>';
    }

    // Determinar el rango de páginas a mostrar (máximo 10 páginas)
    $rangoInicio = max(1, $pagina - 5); // Página inicial del rango
    $rangoFin = min($totalPaginas, $pagina + 4); // Página final del rango

    // Mostrar las páginas dentro del rango calculado
    for ($i = $rangoInicio; $i <= $rangoFin; $i++) {
        $html .= '<li class="page-item ' . ($i == $pagina ? 'active' : '') . '">
                    <button class="page-link" onclick="cargarCiudadanos(' . $i . ')">' . $i . '</button>
                  </li>';
    }

    // Botón de "Siguiente"
    if ($pagina < $totalPaginas) {
        $html .= '<li class="page-item">
                    <button class="page-link" onclick="cargarCiudadanos(' . ($pagina + 1) . ')">Siguiente</button>
                  </li>';
    }

    // Mostrar el botón de "Última" solo si hay más de 10 páginas y la página actual no es la última
    if ($totalPaginas > 10 && $pagina < $totalPaginas) {
        $html .= '<li class="page-item">
                    <button class="page-link" onclick="cargarCiudadanos(' . $totalPaginas . ')">Última</button>
                  </li>';
    }

    $html .= '</ul></nav>';

    return $html;
}
?>