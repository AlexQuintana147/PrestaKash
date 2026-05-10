<script>
document.addEventListener('input', function(e) {
    const el = e.target;
    const isInput = el.tagName === 'INPUT' && (el.type === 'text' || el.type === '' || el.getAttribute('type') === null);
    const isTextarea = el.tagName === 'TEXTAREA';
    
    if ((isInput || isTextarea) && !el.readOnly && !el.disabled) {
        const name = el.name ? el.name.toLowerCase() : '';
        const id = el.id ? el.id.toLowerCase() : '';
        const type = el.type ? el.type.toLowerCase() : '';

        // Excluir campos que no deben ser capitalizados (contraseñas, correos, teléfonos, DNI, etc.)
        if (type === 'password' || type === 'email' || type === 'tel' || type === 'number' ||
            name.includes('password') || id.includes('password') || 
            name.includes('email') || id.includes('email') || 
            name.includes('phone') || id.includes('phone') || 
            name.includes('tel') || id.includes('tel') ||
            name.includes('dni') || id.includes('dni') ||
            name.includes('url') || id.includes('url') ||
            name.includes('website') || id.includes('website') ||
            el.hasAttribute('data-no-capitalize')) {
            return;
        }

        if (el.value.length > 0) {
            const start = el.selectionStart;
            const end = el.selectionEnd;
            const newValue = el.value.charAt(0).toUpperCase() + el.value.slice(1);
            if (el.value !== newValue) {
                el.value = newValue;
                // Restaurar la posición del cursor para evitar saltos
                try {
                    el.setSelectionRange(start, end);
                } catch (err) {}
            }
        }
    }
});
</script>
