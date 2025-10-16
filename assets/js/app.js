/* assets/js/app.js - مصادقة بسيطة + أدوات تنقل */
const LS_USERS = "users";
const LS_CURRENT = "currentUser";

function safeParse(j, f) { try { return JSON.parse(j); } catch { return f; } }
function loadUsers() { return safeParse(localStorage.getItem(LS_USERS), []) || []; }
function saveUsers(u) { localStorage.setItem(LS_USERS, JSON.stringify(u || [])); }
function setCurrentUser(u) { localStorage.setItem(LS_CURRENT, JSON.stringify({ username: u.username, email: u.email })); }
function getCurrentUser() { const u = localStorage.getItem(LS_CURRENT); return u ? safeParse(u, null) : null; }
function logout() { localStorage.removeItem(LS_CURRENT); window.location.href = "../auth/login.html"; }
function requireAuth() { if (!getCurrentUser()) { /* لسهولة التجربة أثناء التطوير، عطّل التحويل التلقائي */ /* window.location.href = "../auth/login.html"; */ } }
function goToHome() { window.location.replace('../home/select-role.html'); }

function attachEnglishValidation(f) {
    if (!f) return;
    f.querySelectorAll("input").forEach(function(i) {
        i.addEventListener("invalid", function(e) {
            e.target.setCustomValidity("");
            if (e.target.validity.valueMissing) {
                e.target.setCustomValidity("Please fill out this field");
            } else if (e.target.type === "email" && e.target.validity.typeMismatch) {
                e.target.setCustomValidity("Please enter a valid email address");
            } else if ((e.target.id === "regPassword" || e.target.id === "regConfirm") && e.target.validity.tooShort) {
                e.target.setCustomValidity("Password must be at least 6 characters");
            } else if (e.target.id === "regConfirm") {
                const password = document.getElementById('regPassword');
                if (password && e.target.value !== password.value) {
                    e.target.setCustomValidity("Passwords do not match");
                }
            }
        });
        i.addEventListener("input", function(e) { e.target.setCustomValidity(""); });
    });
}