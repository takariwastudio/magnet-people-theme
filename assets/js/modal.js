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
  const signinError = document.getElementById("signinError");
  const signinContinueBtn = document.getElementById("signinContinueBtn");
  const signinStepEmail = document.getElementById("signinStepEmail");
  const signinStepCode = document.getElementById("signinStepCode");
  const signinSentTo = document.getElementById("signinSentTo");
  const signinCodeForm = document.getElementById("signinCodeForm");
  const signinCode = document.getElementById("signinCode");
  const signinCodeError = document.getElementById("signinCodeError");
  const signinOpenMail = document.getElementById("signinOpenMail");

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

  /* ── Sign-in sin contraseña: paso 1 email → paso 2 código del correo ── */
  let signinAddress = "";

  async function post(action, fields) {
    const body = new FormData();
    body.append("action", action);
    body.append("nonce", magnetData.loginNonce);
    Object.keys(fields).forEach(function (key) {
      body.append(key, fields[key]);
    });
    const res = await fetch(magnetData.ajaxUrl, { method: "POST", body: body, credentials: "same-origin" });
    return res.json();
  }

  function showError(el, message) {
    el.textContent = message || "";
    el.hidden = !message;
  }

  signinForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    if (!signinEmail.value || !signinEmail.checkValidity()) {
      signinEmail.reportValidity();
      return;
    }
    if (signinContinueBtn.disabled) return;

    showError(signinError, "");
    signinContinueBtn.disabled = true;
    signinContinueBtn.textContent = "Sending…";

    try {
      const json = await post("magnet_send_login", {
        email: signinEmail.value.trim(),
        redirect: window.location.href,
      });
      if (!json.success) throw new Error(json.data && json.data.message);

      signinAddress = signinEmail.value.trim();
      signinSentTo.textContent = signinAddress;

      const provider = json.data && json.data.provider;
      signinOpenMail.hidden = !provider;
      if (provider) {
        signinOpenMail.href = provider.url;
        signinOpenMail.textContent = "Open " + provider.name;
      }

      signinStepEmail.hidden = true;
      signinStepCode.hidden = false;
      signinCode.focus();
    } catch (err) {
      showError(signinError, err.message || "Something went wrong. Please try again.");
    }

    signinContinueBtn.disabled = false;
    signinContinueBtn.textContent = "Continue";
  });

  // Solo dígitos; al llegar a 6 se envía solo
  signinCode.addEventListener("input", function () {
    signinCode.value = signinCode.value.replace(/\D/g, "").slice(0, 6);
    showError(signinCodeError, "");
    if (signinCode.value.length === 6) verifyCode();
  });

  signinCodeForm.addEventListener("submit", function (e) {
    e.preventDefault();
    verifyCode();
  });

  async function verifyCode() {
    if (signinCode.readOnly) return;
    if (signinCode.value.length !== 6) {
      showError(signinCodeError, "Enter the 6-digit code from the email.");
      return;
    }

    signinCode.readOnly = true;
    try {
      const json = await post("magnet_verify_code", { email: signinAddress, code: signinCode.value });
      if (!json.success) throw new Error(json.data && json.data.message);

      // Recargar ya logueado; account.js muestra el aviso "Welcome back!"
      const url = new URL(window.location.href);
      url.searchParams.set("magnet_notice", "signin");
      window.location.href = url.toString();
      return;
    } catch (err) {
      showError(signinCodeError, err.message || "Something went wrong. Please try again.");
      signinCode.select();
    }
    signinCode.readOnly = false;
  }

  // Resetear estado al cerrar el modal de signin
  function resetSigninForm() {
    signinForm.reset();
    signinCodeForm.reset();
    showError(signinError, "");
    showError(signinCodeError, "");
    signinStepEmail.hidden = false;
    signinStepCode.hidden = true;
    signinAddress = "";
  }

  signinClose.addEventListener("click", resetSigninForm);
  signinBackdrop.addEventListener("click", resetSigninForm);
  if (switchToSignup) switchToSignup.addEventListener("click", resetSigninForm);
})();
