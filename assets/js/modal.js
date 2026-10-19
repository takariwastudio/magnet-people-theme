/**
 * modal.js — Signup modal Magnet People v2
 * Añadir al final de main.js
 */
(function () {
  const floatBtn = document.getElementById("signupFloatBtn");
  const modal = document.getElementById("signupModal");
  const backdrop = document.getElementById("modalBackdrop");
  const closeBtn = document.getElementById("modalClose");
  const toggleBtns = document.querySelectorAll(".toggle-btn");
  const paidAmount = document.getElementById("paidAmount");
  const paidPeriod = document.getElementById("paidPeriod");
  const paidCheckout = document.getElementById("paidCheckoutBtn");

  if (!floatBtn || !modal) return;

  // URLs de checkout por período (PMPro level IDs)
  const plans = {
    monthly: { amount: "$35", period: "/month", levelId: 2 },
    yearly: { amount: "$400", period: "/year", levelId: 3 },
  };

  let currentPeriod = "monthly";

  // Abrir modal
  function openModal() {
    modal.hidden = false;
    document.body.style.overflow = "hidden";
    closeBtn.focus();
  }

  // Cerrar modal
  function closeModal() {
    modal.hidden = true;
    document.body.style.overflow = "";
    floatBtn.focus();
  }

  floatBtn.addEventListener("click", function (e) {
    e.preventDefault();
    openModal();
  });

  closeBtn.addEventListener("click", closeModal);
  backdrop.addEventListener("click", closeModal);

  // Cerrar con ESC
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && !modal.hidden) closeModal();
  });

  // Toggle mensual / anual
  toggleBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      const period = this.dataset.period;
      if (period === currentPeriod) return;

      currentPeriod = period;
      const plan = plans[period];

      // Actualizar precio
      paidAmount.textContent = plan.amount;
      paidPeriod.textContent = plan.period;

      // Actualizar URL de checkout
      const url = new URL(paidCheckout.href);
      url.searchParams.set("level", plan.levelId);
      paidCheckout.href = url.toString();

      // Actualizar estilos del toggle
      toggleBtns.forEach(function (b) {
        b.classList.toggle("toggle-btn--active", b.dataset.period === period);
      });
    });
  });
})();
