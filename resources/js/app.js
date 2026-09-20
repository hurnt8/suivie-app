// Livewire and Flux ship their own scripts; this entry only flags that JavaScript is available.
// (It must not be empty: Vite would emit a 0-byte asset, which many upload tools and hosts silently skip → 404.)
document.documentElement.classList.add('js');
