// Only handle password visibility toggle - let PHP handle all validation
document.querySelectorAll(".toggle-password-btn").forEach((btn) => {
  btn.addEventListener("click", function () {
    const targetId = btn.getAttribute("data-target");
    const input = document.getElementById(targetId);
    const img = btn.querySelector("img");
    if (input.type === "password") {
      input.type = "text";
      img.src = "../images/eyeopen.png";
    } else {
      input.type = "password";
      img.src = "../images/eyeclosed.png";
    }
  });
});

// Add a simple console log to confirm the script is loading
console.log("Signup script loaded");