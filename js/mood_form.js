// mood_form.js

document.addEventListener("DOMContentLoaded", () => {
  const tagsInput = document.querySelector("input[name='tags']");

  tagsInput?.addEventListener("blur", () => {
    // Exemplo: remover duplicadas e limpar espaços
    const raw = tagsInput.value;
    const tags = Array.from(new Set(raw.split(',').map(t => t.trim())));
    tagsInput.value = tags.join(', ');
  });
});
