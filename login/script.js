
  document.addEventListener("DOMContentLoaded", () => {
    const togglePassword = document.querySelector(".toggle-password");
    const passwordInput = document.getElementById("password");
    const eyeIcon = togglePassword.querySelector("img");

    togglePassword.addEventListener("click", () => {
      const isPassword = passwordInput.type === "password";
      passwordInput.type = isPassword ? "text" : "password";
      eyeIcon.src = isPassword ? "../images/eyeopen.png" : "../images/eyeclosed.png";
      eyeIcon.alt = isPassword ? "Hide Password" : "Show Password";
    });
  });

