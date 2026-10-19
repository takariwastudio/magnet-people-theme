/**
 * modal.js — Magnet People v2
 * Signup modal (plans) + Sign-in modal
 */
(function () {
  "use strict";

  /* ── Elementos ── */
  const signupModal = document.getElementById("signupModal");
  const signinModal = document.getElementById("signinModal");

  if (!signupModal || !signinModal) return;

  // Signup modal
  const signupBackdrop = document.getElementById("signupBackdrop");
  const signupClose = document.getElementById("signupModalClose");
  const toggleBtns = document.querySelectorAll(".toggle-btn");
  const paidAmount = document.getElementById("paidAmount");
  const paidPeriod = document.getElementById("paidPeriod");
  const paidCheckoutBtn = document.getElementById("paidCheckoutBtn");

  // Signin modal
  const signinBackdrop = document.getElementById("signinBackdrop");
  const signinClose = document.getElementById("signinModalClose");
  const signinForm = document.getElementById("signinForm");
  const signinEmail = document.getElementById("signinEmail");
  const signinPasswordRow = document.getElementById("signinPasswordRow");
  const signinContinueBtn = document.getElementById("signinContinueBtn");

  // Triggers
  const floatBtn = document.getElementById("signupFloatBtn"); // botón flotante
  const signupHeaderBtn = document.getElementById("signupHeaderBtn"); // header desktop
  const signupMobileBtn = document.getElementById("signupMobileBtn"); // header mobile
  const signinBtn = document.getElementById("signinBtn"); // header desktop
  const signinBtnMobile = document.getElementById("signinBtnMobile"); // header mobile
  const switchToSignin = document.getElementById("switchToSignin"); // link en signup modal
  const switchToSignup = document.getElementById("switchToSignup"); // link en signin modal

  /* ── Planes PMPro ── */
  const plans = {
    monthly: { amount: "$35", period: "/month", levelId: 2 },
    yearly: { amount: "$400", period: "/year", levelId: 3 },
  };
  let currentPeriod = "monthly";

  /* ── Helpers ── */
  function openModal(modal, focusEl) {
    closeAll();
    modal.hidden = false;
    document.body.style.overflow = "hidden";
    if (focusEl) focusEl.focus();
  }

  function closeModal(modal, restoreFocusEl) {
    modal.hidden = true;
    if (document.querySelector(".magnet-modal:not([hidden])") === null) {
      document.body.style.overflow = "";
    }
    if (restoreFocusEl) restoreFocusEl.focus();
  }

  function closeAll() {
    [signupModal, signinModal].forEach(function (m) {
      m.hidden = true;
    });
    document.body.style.overflow = "";
  }

  /* ── Abrir Signup ── */
  function openSignup() {
    openModal(signupModal, signupClose);
  }

  /* ── Abrir Signin ── */
  function openSignin() {
    openModal(signinModal, signinEmail);
  }

  /* ── Triggers: botones que abren signup ── */
  [floatBtn, signupHeaderBtn, signupMobileBtn].forEach(function (btn) {
    if (!btn) return;
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      openSignup();
    });
  });

  /* ── Triggers: botones que abren signin ── */
  [signinBtn, signinBtnMobile].forEach(function (btn) {
    if (!btn) return;
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      openSignin();
    });
  });

  /* ── Switch entre modales ── */
  if (switchToSignin) {
    switchToSignin.addEventListener("click", function (e) {
      e.preventDefault();
      openSignin();
    });
  }
  if (switchToSignup) {
    switchToSignup.addEventListener("click", function (e) {
      e.preventDefault();
      openSignup();
    });
  }

  /* ── Cerrar signup ── */
  signupClose.addEventListener("click", function () {
    closeModal(signupModal, floatBtn || signupHeaderBtn);
  });
  signupBackdrop.addEventListener("click", function () {
    closeModal(signupModal, floatBtn || signupHeaderBtn);
  });

  /* ── Cerrar signin ── */
  signinClose.addEventListener("click", function () {
    closeModal(signinModal, signinBtn || signinBtnMobile);
  });
  signinBackdrop.addEventListener("click", function () {
    closeModal(signinModal, signinBtn || signinBtnMobile);
  });

  /* ── ESC cierra cualquier modal abierto ── */
  document.addEventListener("keydown", function (e) {
    if (e.key !== "Escape") return;
    if (!signupModal.hidden)
      closeModal(signupModal, floatBtn || signupHeaderBtn);
    if (!signinModal.hidden) {
      closeModal(signinModal, signinBtn || signinBtnMobile);
      resetSigninForm();
    }
  });

  /* ── Toggle mensual / anual ── */
  toggleBtns.forEach(function (btn) {
    btn.addEventListener("click", function () {
      const period = this.dataset.period;
      if (period === currentPeriod) return;

      currentPeriod = period;
      const plan = plans[period];

      paidAmount.textContent = plan.amount;
      paidPeriod.textContent = plan.period;

      const url = new URL(paidCheckoutBtn.href);
      url.searchParams.set("level", plan.levelId);
      paidCheckoutBtn.href = url.toString();

      toggleBtns.forEach(function (b) {
        b.classList.toggle("toggle-btn--active", b.dataset.period === period);
      });
    });
  });

  /* ── Sign-in form: paso 1 email → paso 2 password ── */
  var emailSubmitted = false;

  signinForm.addEventListener("submit", function (e) {
    // Primera vez que se pulsa "Continue": mostrar campo password
    if (!emailSubmitted) {
      e.preventDefault();
      if (!signinEmail.value || !signinEmail.checkValidity()) {
        signinEmail.reportValidity();
        return;
      }
      // Revelar password
      signinPasswordRow.hidden = false;
      signinContinueBtn.textContent = "Sign in";
      emailSubmitted = true;
      document.getElementById("signinPassword").focus();
      return;
    }
    // Segunda vez: submit real a wp-login.php
    // El form tiene action="wp-login.php" y los campos log + pwd estándar de WP
  });

  // Resetear estado al cerrar el modal de signin
  function resetSigninForm() {
    signinForm.reset();
    signinPasswordRow.hidden = true;
    signinContinueBtn.textContent = "Continue";
    emailSubmitted = false;
  }

  signinClose.addEventListener("click", resetSigninForm);
  signinBackdrop.addEventListener("click", resetSigninForm);
  if (switchToSignup) switchToSignup.addEventListener("click", resetSigninForm);
})();
