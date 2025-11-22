/* ===============================
     Expense Tracker - app.js
     This file contains the frontend bootstrapping and page-specific logic.
     It preserves the original frontend behavior and points API calls to
     the local Laravel backend at http://127.0.0.1:8000/
     =============================== */

// API base used by frontend modules
const API = "http://127.0.0.1:8000";
window.API = API;

// Token helpers
function setToken(t) {
    localStorage.setItem("token", t);
}
function getToken() {
    return localStorage.getItem("token");
}
window.setToken = setToken;
window.getToken = getToken;

/* ---------------------------
     LOGIN & REGISTER
     --------------------------- */
document.addEventListener("DOMContentLoaded", () => {
    // If already logged in and on index, redirect
    try {
        const token = getToken();
        const path = window.location.pathname || "";
        const isIndex =
            path === "/" || path.endsWith("/index.html") || path === "";
        if (token && isIndex) {
            window.location.href = "/dashboard.html";
            return;
        }
    } catch (e) {
        console.warn(e);
    }

    // Login form
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
    if (loginForm) {
        const showRegister = document.getElementById("showRegister");
        if (showRegister) {
            showRegister.addEventListener("click", (ev) => {
                ev.preventDefault();
                if (registerForm) registerForm.classList.remove("hidden");
                loginForm.classList.add("hidden");
            });
        }

        const showLogin = document.getElementById("showLogin");
        if (showLogin && registerForm) {
            showLogin.addEventListener("click", (ev) => {
                ev.preventDefault();
                registerForm.classList.add("hidden");
                loginForm.classList.remove("hidden");
            });
        }

        loginForm.addEventListener("submit", async (ev) => {
            ev.preventDefault();
            const email = document.getElementById("email").value;
            const password = document.getElementById("password").value;
            try {
                const res = await fetch(`${API}/auth/login`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ email, password }),
                });
                const data = await res.json();
                if (res.ok) {
                    setToken(data.token);
                    window.location = "/dashboard.html";
                } else {
                    alert(data.message || "Login failed");
                }
            } catch (err) {
                console.error(err);
                alert("Network error");
            }
        });
    }

    if (registerForm) {
        registerForm.addEventListener("submit", async (ev) => {
            ev.preventDefault();
            const name = document.getElementById("rname").value;
            const email = document.getElementById("remail").value;
            const password = document.getElementById("rpassword").value;
            try {
                const res = await fetch(`${API}/auth/register`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ name, email, password }),
                });
                const data = await res.json();
                if (res.ok) {
                    alert("Registered. Please login.");
                    registerForm.classList.add("hidden");
                    if (loginForm) loginForm.classList.remove("hidden");
                } else {
                    alert(data.message || "Register failed");
                }
            } catch (err) {
                console.error(err);
                alert("Network error");
            }
        });
    }
});

/* ---------------------------
     DASHBOARD & EXPENSES
     --------------------------- */
if (document.getElementById("expenseForm")) {
    const token = getToken();
    if (!token) window.location = "/";

    // Logout
    const logout = document.getElementById("logout");
    if (logout)
        logout.addEventListener("click", () => {
            localStorage.removeItem("token");
            window.location = "/";
        });

    // Load categories
    async function loadCategories() {
        try {
            const res = await fetch(`${API}/categories`, {
                headers: { Authorization: "Bearer " + token },
            });
            const data = await res.json();
            if (!res.ok) return [];
            return data;
        } catch (e) {
            return [];
        }
    }

    async function loadExpenses() {
        try {
            const res = await fetch(`${API}/expenses`, {
                headers: { Authorization: "Bearer " + token },
            });
            if (res.status === 401) {
                localStorage.removeItem("token");
                window.location = "/";
                return;
            }
            const data = await res.json();
            renderExpenses(data || []);
        } catch (e) {
            console.error(e);
        }
    }

    function renderExpenses(items) {
        const list = document.getElementById("expensesList");
        if (!list) return;
        list.innerHTML = "";
        if (!items || items.length === 0) {
            list.innerHTML =
                '<div class="list-card"><p class="muted">No expenses yet.</p></div>';
            return;
        }
        items.forEach((exp) => {
            const el = document.createElement("div");
            el.className = "item-card";
            el.innerHTML = `<div class="item-badge">$</div><div class="item-body"><div class="item-title">${formatCurrency(
                exp.amount
            )} — ${exp.note || ""}</div><div class="item-sub">${
                exp.date || ""
            }</div></div>`;
            list.appendChild(el);
        });
    }

    // Expense form submit
    document
        .getElementById("expenseForm")
        .addEventListener("submit", async (ev) => {
            ev.preventDefault();
            const form = document.getElementById("expenseForm");
            const fd = new FormData(form);
            try {
                const res = await fetch(`${API}/expenses`, {
                    method: "POST",
                    headers: { Authorization: "Bearer " + token },
                    body: fd,
                });
                const data = await res.json();
                if (res.ok) {
                    alert("Expense added");
                    loadExpenses();
                } else {
                    alert(data.message || "Error");
                }
            } catch (e) {
                console.error(e);
                alert("Network error");
            }
        });

    loadCategories();
    loadExpenses();
}

/* ---------------------------
     Utilities used by pages
     --------------------------- */
function formatCurrency(n) {
    try {
        return Number(n).toLocaleString(undefined, {
            style: "currency",
            currency: "USD",
        });
    } catch (e) {
        return n;
    }
}

// Simple apiFetch wrapper used by some modules
window.apiFetch = async function (path, opts = {}) {
    const token = getToken();
    const headers = opts.headers || {};
    if (token) headers["Authorization"] = "Bearer " + token;
    try {
        const res = await fetch(path.startsWith("http") ? path : API + path, {
            ...opts,
            headers,
        });
        let body = null;
        try {
            body = await res.json();
        } catch (e) {
            body = await res.text();
        }
        return { ok: res.ok, status: res.status, body };
    } catch (e) {
        return { ok: false, status: 0, body: null, error: e };
    }
};

// basic UI helpers
window.showToast = function (msg) {
    alert(msg);
};
