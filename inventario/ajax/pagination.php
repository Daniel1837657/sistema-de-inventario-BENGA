<?php
function paginate($reload, $page, $tpages, $adjacents) {
    $prevlabel = '<i class="bi bi-chevron-left"></i> Anterior';
    $nextlabel = 'Siguiente <i class="bi bi-chevron-right"></i>';
    
    // Si solo hay una página, no mostrar paginación
    if ($tpages <= 1) {
        return '';
    }
    
    $out = '<nav aria-label="Navegación de páginas"><ul class="pagination justify-content-center">';

    // Validar página actual
    $page = max(1, min($page, $tpages));

    // Previous
    if ($page == 1) {
        $out .= '<li class="page-item disabled"><span class="page-link">' . $prevlabel . '</span></li>';
    } else {
        $out .= '<li class="page-item"><a class="page-link" href="#" onclick="load(' . ($page - 1) . '); return false;">' . $prevlabel . '</a></li>';
    }

    // First page
    if ($page > ($adjacents + 1)) {
        $out .= '<li class="page-item"><a class="page-link" href="#" onclick="load(1); return false;">1</a></li>';
    }

    // Interval
    if ($page > ($adjacents + 2)) {
        $out .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    // Pages range
    $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;
    $pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;

    for ($i = $pmin; $i <= $pmax; $i++) {
        if ($i == $page) {
            $out .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
        } else {
            $out .= '<li class="page-item"><a class="page-link" href="#" onclick="load(' . $i . '); return false;">' . $i . '</a></li>';
        }
    }

    // Interval
    if ($page < ($tpages - $adjacents - 1)) {
        $out .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
    }

    // Last page
    if ($page < ($tpages - $adjacents)) {
        $out .= '<li class="page-item"><a class="page-link" href="#" onclick="load(' . $tpages . '); return false;">' . $tpages . '</a></li>';
    }

    // Next
    if ($page < $tpages) {
        $out .= '<li class="page-item"><a class="page-link" href="#" onclick="load(' . ($page + 1) . '); return false;">' . $nextlabel . '</a></li>';
    } else {
        $out .= '<li class="page-item disabled"><span class="page-link">' . $nextlabel . '</span></li>';
    }

    $out .= '</ul></nav>';

    // Información de páginas
    $out .= '<div class="text-center mt-2"><small class="text-muted">Página ' . $page . ' de ' . $tpages . '</small></div>';

    return $out;
}

// Función para cargar datos AJAX
function loadData($reload, $containerId = 'resultados') {
    $out = <<<EOT
<script>
function load(page) {
    var container = document.getElementById('$containerId');
    if (!container) {
        console.error('No se encontró el contenedor para cargar datos.');
        return;
    }
    
    // Mostrar loader
    container.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div></div>';
    
    var xhr = new XMLHttpRequest();
    xhr.open('GET', '$reload?action=ajax&page=' + page, true);
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                container.innerHTML = xhr.responseText;
            } else {
                container.innerHTML = '<div class="alert alert-danger text-center" role="alert"><i class="bi bi-exclamation-triangle me-2"></i>Error cargando datos. Intenta de nuevo.</div>';
            }
        }
    };
    xhr.send();
}
</script>
EOT;

    return $out;
}
?>
