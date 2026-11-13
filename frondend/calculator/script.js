
let display = document.getElementById("display");

function appendValue(value) {
    if (display.value === "Error") {
        display.value = "";
    }

    if (display.value === "0") {
        display.value = "";
    }

    display.value += value;
}

function clearDisplay() {
    display.value = "0";
}

function deleteLast() {
    display.value = display.value.slice(0, -1);

    if (display.value === "") {
        display.value = "0";
    }
}

function calculate() {
    try {
        let expression = display.value;

        if (
            expression === "" ||
            !/^[0-9+\-*/%.()\s]+$/.test(expression)
        ) {
            throw new Error("Invalid");
        }

        let result = Function(
            '"use strict"; return (' + expression + ')'
        )();

        if (!Number.isFinite(result)) {
            throw new Error("Invalid");
        }

        display.value = result;
    }
    catch {
        display.value = "Error";
    }
}
