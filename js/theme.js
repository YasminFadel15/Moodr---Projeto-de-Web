tailwind.config = {
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        roxo: '#7C3AED'
      }
    }
  }
}

// theme.js
document.addEventListener("DOMContentLoaded", () => {
  const root = document.documentElement;
  const toggle = document.getElementById("theme-toggle");

  // Carrega preferência
  if (localStorage.getItem("theme") === "dark") {
    root.classList.add("dark");
  }

  // Alternar tema
  toggle?.addEventListener("click", () => {
    root.classList.toggle("dark");
    const newTheme = root.classList.contains("dark") ? "dark" : "light";
    localStorage.setItem("theme", newTheme);
  });
});
