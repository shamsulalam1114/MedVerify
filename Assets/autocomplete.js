let autocompleteTimeout;
const DEBOUNCE_DELAY = 300;

function initAutocomplete(inputId, endpoint, minChars = 2) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    const autocompleteContainer = document.createElement('div');
    autocompleteContainer.className = 'autocomplete-suggestions';
    autocompleteContainer.style.cssText = `
        position: absolute;
        background: white;
        border: 1px solid #ccc;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    `;
    input.parentNode.style.position = 'relative';
    input.parentNode.appendChild(autocompleteContainer);
    
    input.addEventListener('input', function() {
        clearTimeout(autocompleteTimeout);
        const query = this.value.trim();
        
        if (query.length < minChars) {
            autocompleteContainer.style.display = 'none';
            return;
        }
        
        autocompleteTimeout = setTimeout(() => {
            fetch(endpoint + '?q=' + encodeURIComponent(query))
                .then(response => response.json())
                .then(data => {
                    autocompleteContainer.innerHTML = '';
                    
                    if (data.length === 0) {
                        autocompleteContainer.style.display = 'none';
                        return;
                    }
                    
                    data.forEach(item => {
                        const div = document.createElement('div');
                        div.textContent = item;
                        div.style.cssText = 'padding: 8px 12px; cursor: pointer;';
                        div.onmouseover = function() { this.style.backgroundColor = '#f0f0f0'; };
                        div.onmouseout = function() { this.style.backgroundColor = 'white'; };
                        div.onclick = function() {
                            input.value = item;
                            autocompleteContainer.style.display = 'none';
                        };
                        autocompleteContainer.appendChild(div);
                    });
                    
                    autocompleteContainer.style.width = input.offsetWidth + 'px';
                    autocompleteContainer.style.display = 'block';
                })
                .catch(error => console.error('Autocomplete error:', error));
        }, DEBOUNCE_DELAY);
    });
    
    document.addEventListener('click', function(e) {
        if (e.target !== input) {
            autocompleteContainer.style.display = 'none';
        }
    });
}
