/**
 * account.js — Magnet People v2
 * Avisos (toast) + modal de cuenta con sus vistas internas
 */
(function () {
  "use strict";

  /* ── Toast ── */
  const notices = {
    signin: ["Welcome back! You've successfully signed in.", "success"],
    "link-expired": ["Your sign-in link has expired. Please request a new one.", "error"],
  };

  function toast(text, type) {
    const el = document.createElement("div");
    el.className = "magnet-toast magnet-toast--" + (type || "success");
    el.setAttribute("role", "status");
    el.textContent = text;
    document.body.appendChild(el);
    el.offsetHeight; // fuerza reflow para que corra la transición
    el.classList.add("is-visible");
    setTimeout(function () {
      el.classList.remove("is-visible");
      setTimeout(function () { el.remove(); }, 400);
    }, 4000);
  }

  // ?magnet_notice=signin (lo pone el login) → mostrar y limpiar la URL
  const url = new URL(window.location.href);
  const notice = url.searchParams.get("magnet_notice");
  if (notice) {
    if (notices[notice]) toast(notices[notice][0], notices[notice][1]);
    url.searchParams.delete("magnet_notice");
    history.replaceState(null, "", url.toString());
  }

  /* ── Modal de cuenta ── */
  const modal = document.getElementById("accountModal");
  if (!modal) return;

  const box = modal.querySelector(".magnet-modal__box");
  const closeBtn = modal.querySelector(".magnet-modal__close");
  const views = modal.querySelectorAll("[data-view]");
  const profileForm = document.getElementById("accountProfileForm");
  const emailsForm = document.getElementById("accountEmailsForm");
  const unsubscribeAll = document.getElementById("accountUnsubscribeAll");
  const accountHash = /^#(account|\/portal\/account)/; // también los enlaces viejos de Ghost
  let lastTrigger = null;

  function showView(name) {
    views.forEach(function (view) {
      view.hidden = view.dataset.view !== name;
    });
    modal.classList.toggle("account-modal--full", name === "gift");
    if (name === "profile") profileForm.reset();
    box.scrollTop = 0;
  }

  function openAccount(trigger) {
    lastTrigger = trigger || null;
    showView("home");
    modal.hidden = false;
    document.body.style.overflow = "hidden";
    closeBtn.focus();
  }

  function closeAccount() {
    modal.hidden = true;
    document.body.style.overflow = "";
    if (accountHash.test(window.location.hash)) {
      history.replaceState(null, "", window.location.pathname + window.location.search);
    }
    if (lastTrigger) lastTrigger.focus();
  }

  document.querySelectorAll("[data-account-open]").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      openAccount(btn);
    });
  });
  modal.querySelectorAll("[data-account-close]").forEach(function (el) {
    el.addEventListener("click", closeAccount);
  });
  modal.querySelectorAll("[data-account-go]").forEach(function (el) {
    el.addEventListener("click", function (e) {
      e.preventDefault();
      showView(el.dataset.accountGo);
    });
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && !modal.hidden) closeAccount();
  });

  // /#account abre el modal directo
  function openFromHash() {
    if (accountHash.test(window.location.hash) && modal.hidden) openAccount();
  }
  openFromHash();
  window.addEventListener("hashchange", openFromHash);

  /* ── AJAX ── */
  async function post(action, body) {
    body.append("action", action);
    body.append("nonce", magnetData.accountNonce);
    const res = await fetch(magnetData.ajaxUrl, { method: "POST", body: body, credentials: "same-origin" });
    const json = await res.json();
    if (!json.success) throw new Error(json.data && json.data.message);
    return json.data;
  }

  /* ── Account settings ── */
  profileForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    if (!profileForm.reportValidity()) return;

    const btn = profileForm.querySelector('[type="submit"]');
    const error = profileForm.querySelector(".signin-form__error");
    if (btn.disabled) return;

    btn.disabled = true;
    btn.textContent = "Saving…";
    error.hidden = true;

    try {
      const data = await post("magnet_update_account", new FormData(profileForm));

      // Reflejar los cambios en la vista principal y como nuevo valor inicial del form
      modal.querySelectorAll("[data-account-name]").forEach(function (el) { el.textContent = data.name; });
      modal.querySelectorAll("[data-account-email]").forEach(function (el) { el.textContent = data.email; });
      profileForm.elements.namedItem("name").defaultValue = data.name;
      profileForm.elements.namedItem("email").defaultValue = data.email;

      showView("home");
      toast("Account details updated successfully.");
    } catch (err) {
      error.textContent = err.message || "Something went wrong. Please try again.";
      error.hidden = false;
    }

    btn.disabled = false;
    btn.textContent = "Save";
  });

  /* ── Email preferences: se guardan al cambiar cada toggle ── */
  async function savePrefs(message) {
    try {
      await post("magnet_email_prefs", new FormData(emailsForm));
      if (message) toast(message);
    } catch (err) {
      toast(err.message || "Couldn't save your preferences. Please try again.", "error");
    }
  }

  emailsForm.addEventListener("change", function () {
    savePrefs();
  });

  unsubscribeAll.addEventListener("click", function () {
    emailsForm.querySelectorAll(".account-toggle").forEach(function (toggle) { toggle.checked = false; });
    savePrefs("You've been unsubscribed from all emails.");
  });
})();
