const use = document.getElementById("use");
const teachType = document.getElementById("teachType");
const priceInputContainer = document.getElementById("priceInputContainer");

// Manejo de pasos
const step0Container = document.getElementById('step0');
const step1Container = document.getElementById("languageInputContainer");
const step2Container = document.getElementById("sublanguagesContainer");
const step3Container = document.getElementById("levelSelection");
const step4Container = document.getElementById("teach");
const teachFM = document.getElementById("teachType")
const step5Container = document.getElementById("priceInputContainer");
const nextStepButton = document.getElementById("nextStep");
let hasSublanguages = false;
const submitButton = document.getElementById("submit");

export function updateStepView() {
    step0Container.style.display = 'none';
    step1Container.style.display = 'none';
    step2Container.style.display = 'none';
    step3Container.style.display = 'none';
    step4Container.style.display = 'none';
    step5Container.style.display = 'none';
    submitButton.style.display = 'none';
    nextStepButton.disabled = false;

    switch (step) {
        case 0:
            step0Container.style.display = 'block';
            break;

        case 1:
            step1Container.style.display = 'block';
            break;

        case 2:
            step2Container.style.display = 'block';
            break;

        case 3:
            step3Container.style.display = 'block';
            break;

        case 4:
            step4Container.style.display = 'block';
            nextStepButton.disabled = true;
            break;

        case 5:
            if (teachFM.value == 'tfm') {
                step5Container.style.display = 'block';
                nextStepButton.disabled = true;
                submitButton.style.display = 'block';
            }
            break;

        default:
            step0Container.style.display = 'block';
            break;
    }

    if (step >= 3 && use.value == 'learn') {
        nextStepButton.style.display = "none";
        nextStepButton.disabled = true;
        submitButton.style.display = 'block';
    } else if (step >= 4 && use.value == 'know') {
        nextStepButton.style.display;
    }

    if ((step >= 5 && teachFM.value == 'e') || (step >= 5 && teachFM.value == 'jc')) {
        nextStepButton.style.display = "none";
    }

    if (step < 5) {
        nextStepButton.style.display = 'inline-block';
    }
    if(useOfLanguage === 'learn'){
        if(step === 3){
            submitButton.style.display = 'block';
            nextStepButton.disabled = true;
        }
    }
    
    if (step === 3){
        console.log(use.value);
        console.log(useOfLanguage);
        console.log(step);
    
    }
}

teachFM.addEventListener('change', function () {
    if (teachFM.value === 'e' || teachFM.value === 'jc') { nextStepButton.disabled = true; submitButton.style.display = 'block'; }
    else if (teachFM.value === 'tfm') { nextStepButton.disabled = false;  }

});


async function generateCheckboxes(sublanguages, containerId = "sublanguagesList") {
    let div = document.getElementById(containerId);
    
    div.innerHTML = "";
    sublanguages.forEach(item => {
        let checkbox = document.createElement("input");
        checkbox.type = "checkbox";
        checkbox.value = item.Id;
        checkbox.id = `checkbox-${item.Id}`;
        checkbox.name = "sublanguages[]";

        let label = document.createElement("label");
        label.htmlFor = `checkbox-${item.Id}`;
        label.textContent = item.Name;

        // Evento para limitar la selección a 7 checkboxes
        checkbox.addEventListener("change", () => {
            let checkboxes = div.querySelectorAll("input[type='checkbox']");
            let checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
            checkboxes.forEach(cb => cb.disabled = checkedCount >= 7 && !cb.checked);
        });

        div.appendChild(checkbox);
        div.appendChild(label);
        div.appendChild(document.createElement("br"));
    });


}

export async function loadSublanguages(langId) {
    if (!langId) {
        console.warn("No se proporcionó un ID de idioma.");
        return false;
    }

    try {
        const response = await fetch(`api.php?lang=${encodeURIComponent(langId)}`);
        if (!response.ok) {
            throw new Error(`Error en la petición: ${response.statusText}`);
        }

        const data = await response.json();
        console.log("Datos recibidos:", data);

        let sublanguages = data.sublanguages || [];
        hasSublanguages = sublanguages.length > 0;

        generateCheckboxes(sublanguages);

        document.getElementById("sublanguagesContainer").style.display = hasSublanguages ? "block" : "none";

        return hasSublanguages;
    } catch (error) {
        console.error("Error cargando sublenguajes:", error);
        generateCheckboxes([]);
        hasSublanguages = false;
        return false;
    }
}




window.onload = function () {

    document.getElementById("nextStep").addEventListener("click", async function () {
        if (step === 1) {
            let selectedLang = document.getElementById("languageInput").value.trim();
            if (selectedLang) {
                hasSublanguages = await loadSublanguages(selectedLang);
                // console.log("hasSublanguages después de carga:", hasSublanguages);
            }

            if (!hasSublanguages) {
                step += 2;
            } else {
                step += 1;
            }
        } else if (step < 5) {
            step++;
        }

        console.log("Paso actualizado a:", step);
        updateStepView();
    });

    document.getElementById("prevStep").addEventListener("click", function () {
        if (step === 3 && !hasSublanguages) {
            step -= 2;
        } else if (step > 0) {
            step--;
        }
        console.log(step);
        updateStepView();
    });

    updateStepView();

    // STEP 1: Búsqueda de idiomas con autocompletado
    const langInput = document.getElementById("languageInput");

    langInput.addEventListener("input", async function () {
        let searchText = langInput.value.trim();
        if (!searchText) return;

        try {
            const response = await fetch(`api.php?searchText=${encodeURIComponent(searchText)}`);
            if (!response.ok) throw new Error("Error en la petición");

            const data = await response.json();
            let datalist = document.getElementById("languageOptions");
            datalist.innerHTML = "";

            data.languages.forEach(lang => {
                let option = document.createElement("option");
                option.value = lang.Id;
                option.textContent = `${lang.Name} (${lang.Id})`;
                datalist.appendChild(option);
            });

        } catch (error) {
            console.error("Error buscando idiomas:", error);
        }
    });

    // STEP 2: Cargar sublenguajes dinámicamente al seleccionar un idioma
    langInput.addEventListener("change", async function () {
        let selectedLang = langInput.value.trim();
        if (!selectedLang) return;

        try {
            const response = await fetch(`api.php?lang=${encodeURIComponent(selectedLang)}`);
            if (!response.ok) throw new Error(`Error en la petición: ${response.statusText}`);

            const data = await response.json();
            hasSublanguages = data.sublanguages?.length > 0;
            // console.log("hasSublanguages definido en change:", hasSublanguages);
        } catch (error) {
            console.error("Error cargando sublenguajes:", error);
            hasSublanguages = false;
        }
    });
};
