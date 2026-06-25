document.addEventListener('DOMContentLoaded', function () {
    const card = document.getElementById('card-reporte-voz');
    if (!card) return; // El usuario no tiene permiso CU22_GEN, la tarjeta no se renderizó.

    const btnGrabar = document.getElementById('btn-grabar-voz');
    const iconoGrabar = document.getElementById('icono-grabar-voz');
    const textoGrabar = document.getElementById('texto-grabar-voz');
    const estadoVoz = document.getElementById('estado-voz');
    const resultadoWrap = document.getElementById('resultado-voz-wrap');
    const textoTranscripcion = document.getElementById('texto-transcripcion');
    const resultadoContenido = document.getElementById('resultado-voz-contenido');
    const audioRespuesta = document.getElementById('audio-respuesta-voz');
    const accionesExportar = document.getElementById('acciones-exportar-voz');
    const btnExportarPdf = document.getElementById('btn-exportar-pdf');
    const btnExportarExcel = document.getElementById('btn-exportar-excel');

    let mediaRecorder = null;
    let chunks = [];
    let grabando = false;
    let ultimoResultado = null; // se guarda para poder exportarlo después

    // Gemini solo acepta WAV, MP3, AIFF, AAC, OGG o FLAC como audio de entrada.
    // El navegador, por defecto, suele grabar en WebM/Opus, que NO está soportado.
    // Por eso se fuerza un tipo compatible, probando en orden de preferencia.
    function elegirMimeType() {
        const candidatos = [
            'audio/ogg;codecs=opus',
            'audio/ogg',
            'audio/mp4',
            'audio/aac',
            'audio/webm;codecs=opus', // último recurso; si llega aquí, se avisa al backend igual
        ];
        return candidatos.find((tipo) => MediaRecorder.isTypeSupported(tipo)) || '';
    }

    btnGrabar.addEventListener('click', async function () {
        if (grabando) {
            mediaRecorder.stop();
            return;
        }

        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            const mimeType = elegirMimeType();

            mediaRecorder = mimeType
                ? new MediaRecorder(stream, { mimeType })
                : new MediaRecorder(stream);

            chunks = [];

            mediaRecorder.ondataavailable = (e) => {
                if (e.data.size > 0) chunks.push(e.data);
            };

            mediaRecorder.onstop = function () {
                stream.getTracks().forEach((track) => track.stop());
                const tipoFinal = mediaRecorder.mimeType || mimeType || 'audio/ogg';
                const blob = new Blob(chunks, { type: tipoFinal });
                enviarConsulta(blob, tipoFinal);
            };

            mediaRecorder.start();
            grabando = true;
            iconoGrabar.textContent = '⏹';
            textoGrabar.textContent = 'Detener';
            estadoVoz.textContent = 'Escuchando…';
            ocultarResultadoAnterior();
        } catch (error) {
            estadoVoz.textContent = 'No se pudo acceder al micrófono.';
            console.error('Error al acceder al micrófono:', error);
        }
    });

    function ocultarResultadoAnterior() {
        resultadoWrap.style.display = 'none';
        audioRespuesta.style.display = 'none';
        accionesExportar.style.display = 'none';
        ultimoResultado = null;
    }

    async function enviarConsulta(blob, mimeType) {
        grabando = false;
        iconoGrabar.textContent = '🎤';
        textoGrabar.textContent = 'Hablar';
        estadoVoz.textContent = 'Procesando tu consulta…';
        btnGrabar.disabled = true;

        const formData = new FormData();
        formData.append('audio', blob, 'consulta.' + (mimeType.split('/')[1] || 'ogg').split(';')[0]);
        formData.append('mime_type', mimeType);

        try {
            const respuesta = await fetch('/api/reporte/consultar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });

            const datos = await respuesta.json();
            mostrarResultado(datos);
        } catch (error) {
            estadoVoz.textContent = 'Ocurrió un error al procesar la consulta.';
            console.error('Error al enviar la consulta de voz:', error);
        } finally {
            btnGrabar.disabled = false;
        }
    }

    function mostrarResultado(datos) {
        estadoVoz.textContent = '';
        resultadoWrap.style.display = 'block';

        textoTranscripcion.textContent = datos.transcripcion || '(no se detectó texto)';

        if (datos.ok) {
            resultadoContenido.innerHTML = renderizarResultado(datos.funcion, datos.resultado);
            ultimoResultado = datos;
            accionesExportar.style.display = 'flex';
        } else {
            resultadoContenido.innerHTML = '<p style="color:var(--danger);">' + escaparHtml(datos.mensaje) + '</p>';
            accionesExportar.style.display = 'none';
            ultimoResultado = null;
        }

        if (datos.audio_base64) {
            const audioBlob = base64AAudioBlob(datos.audio_base64, datos.audio_mime || 'audio/wav');
            audioRespuesta.src = URL.createObjectURL(audioBlob);
            audioRespuesta.style.display = 'block';
            audioRespuesta.play().catch(() => {
                // Algunos navegadores bloquean el autoplay; el usuario puede darle play manualmente.
            });
        }
    }

    function base64AAudioBlob(base64, mimeType) {
        const binario = atob(base64);
        const bytes = new Uint8Array(binario.length);
        for (let i = 0; i < binario.length; i++) {
            bytes[i] = binario.charCodeAt(i);
        }
        return new Blob([bytes], { type: mimeType });
    }

    // Renderiza el resultado de forma genérica: como el catálogo de CU-22 tiene
    // 14 funciones distintas, cada una con una forma de datos diferente, se
    // muestra como una tabla clave-valor simple en vez de una vista a medida
    // por cada función.
    function renderizarResultado(nombreFuncion, resultado) {
        if (resultado === null || resultado === undefined) {
            return '<p style="color:var(--muted);">No se encontraron datos.</p>';
        }

        if (Array.isArray(resultado)) {
            return renderizarLista(resultado);
        }

        if (typeof resultado === 'object') {
            const entradas = Object.entries(resultado);

            // Caso especial: una sola clave y es una lista — se muestra el
            // título como encabezado de sección, con la tabla debajo, en
            // vez de como una fila de tabla horizontal (que se ve mal con
            // contenido tan ancho al lado de la etiqueta).
            if (entradas.length === 1 && Array.isArray(entradas[0][1])) {
                const [clave, lista] = entradas[0];
                return `<div style="font-weight:600; margin-bottom:.6rem; text-transform:capitalize;">${escaparHtml(clave)}</div>` + renderizarLista(lista);
            }

            let html = '<div class="table-wrap" style="border:none;"><table>';
            for (const [clave, valor] of entradas) {
                if (Array.isArray(valor)) {
                    html += `<tr><td colspan="2" style="font-weight:600; padding-top:1rem;">${escaparHtml(clave)}</td></tr><tr><td colspan="2">${renderizarLista(valor)}</td></tr>`;
                } else {
                    html += `<tr><td style="font-weight:600;">${escaparHtml(clave)}</td><td>${escaparHtml(String(valor ?? '—'))}</td></tr>`;
                }
            }
            html += '</table></div>';
            return html;
        }

        return '<p>' + escaparHtml(String(resultado)) + '</p>';
    }

    function renderizarLista(lista) {
        if (!lista.length) {
            return '<p style="color:var(--muted); font-size:.85rem;">(sin elementos)</p>';
        }
        if (typeof lista[0] === 'object') {
            const columnas = Object.keys(lista[0]);
            let html = '<div class="table-wrap" style="border:none;"><table><thead><tr>';
            columnas.forEach((col) => (html += `<th>${escaparHtml(col)}</th>`));
            html += '</tr></thead><tbody>';
            lista.forEach((fila) => {
                html += '<tr>';
                columnas.forEach((col) => {
                    const valor = fila[col];
                    html += `<td>${escaparHtml(Array.isArray(valor) ? valor.join(', ') : String(valor ?? '—'))}</td>`;
                });
                html += '</tr>';
            });
            html += '</tbody></table></div>';
            return html;
        }
        return '<ul>' + lista.map((item) => `<li>${escaparHtml(String(item))}</li>`).join('') + '</ul>';
    }

    function escaparHtml(texto) {
        const div = document.createElement('div');
        div.textContent = texto;
        return div.innerHTML;
    }

    btnExportarPdf.addEventListener('click', function () {
        if (!ultimoResultado) return;
        exportarResultado('pdf');
    });

    btnExportarExcel.addEventListener('click', function () {
        if (!ultimoResultado) return;
        exportarResultado('excel');
    });

    function exportarResultado(formato) {
        // No se usa fetch + descarga manual: se navega directo a la URL como
        // un formulario POST, así el navegador maneja la descarga del archivo
        // (PDF/Excel) igual que cualquier enlace de descarga normal del sistema.
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/api/reporte/exportar';
        form.style.display = 'none';

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = document.querySelector('meta[name="csrf-token"]').content;
        form.appendChild(csrf);

        const campoFuncion = document.createElement('input');
        campoFuncion.type = 'hidden';
        campoFuncion.name = 'funcion';
        campoFuncion.value = ultimoResultado.funcion;
        form.appendChild(campoFuncion);

        const campoFormato = document.createElement('input');
        campoFormato.type = 'hidden';
        campoFormato.name = 'formato';
        campoFormato.value = formato;
        form.appendChild(campoFormato);

        Object.entries(ultimoResultado.parametros || {}).forEach(([clave, valor]) => {
            const campo = document.createElement('input');
            campo.type = 'hidden';
            campo.name = 'parametros[' + clave + ']';
            campo.value = valor;
            form.appendChild(campo);
        });

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
});