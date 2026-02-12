(function () {
    // Persist last message so Livewire re-renders don't erase it
    let __cnpLastMessage = null;
    let __cnpDebounceTimer = null;

    // Main check function
    window.cnpCheck = function (cnp) {
        const el = document.getElementById("cnp-check");
        if (!el) return;
        if (!cnp) {
            el.innerText = "Enter CNP to validate";
            __cnpLastMessage = null;
            return;
        }

        // debounce rapid input
        if (__cnpDebounceTimer) clearTimeout(__cnpDebounceTimer);
        __cnpDebounceTimer = setTimeout(() => {
            fetch("/cnp-check?cnp=" + encodeURIComponent(cnp))
                .then((r) => r.json())
                .then((data) => {
                    if (data.valid) {
                        if (data.exists) {
                            __cnpLastMessage =
                                '<div style="color:orange">CNP exists in database: <strong>' +
                                (data.name || "") +
                                "</strong></div>";
                            el.innerHTML = __cnpLastMessage;
                        } else {
                            __cnpLastMessage =
                                '<div style="color:green">CNP valid</div>';
                            el.innerHTML = __cnpLastMessage;
                        }
                    } else {
                        __cnpLastMessage =
                            '<div style="color:red">' +
                            (data.message || "Invalid CNP") +
                            "</div>";
                        el.innerHTML = __cnpLastMessage;
                    }
                })
                .catch(() => {
                    __cnpLastMessage = "Validation service unavailable";
                    el.innerText = __cnpLastMessage;
                });
        }, 300);
    };

    // Attach input listeners to any CNP input fields
    function attachListeners() {
        const inputs = document.querySelectorAll(
            'input[name*="cnp" i], input[id*="cnp" i], input[placeholder*="cnp" i], input[aria-label*="cnp" i]',
        );
        inputs.forEach((input) => {
            if (input.dataset.cnpListenerBound) return;
            input.addEventListener("input", function () {
                if (typeof window.cnpCheck === "function") {
                    console.debug(
                        "[cnp-check] input event bound, value=",
                        this.value,
                    );
                    window.cnpCheck(this.value);
                }
            });
            input.dataset.cnpListenerBound = "1";
            console.debug("[cnp-check] bound to input", input);
        });

        // restore last message after Livewire re-render
        const el = document.getElementById("cnp-check");
        if (el && __cnpLastMessage) {
            el.innerHTML = __cnpLastMessage;
        }
    }

    // Initial attach on DOM ready
    if (document.readyState === "loading") {
        document.addEventListener("DOMContentLoaded", attachListeners);
    } else {
        attachListeners();
    }

    // Re-attach after Livewire updates (single-page navigation)
    document.addEventListener("livewire:load", function () {
        attachListeners();
        if (window.Livewire && typeof window.Livewire.hook === "function") {
            window.Livewire.hook("message.processed", attachListeners);
        } else {
            document.addEventListener("livewire:update", attachListeners);
        }
    });
})();
