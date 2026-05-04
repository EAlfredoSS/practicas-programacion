const use = document.getElementById("use")
const teachType = document.getElementById("teachType")
const priceInputContainer = document.getElementById("priceInputContainer")

// Manejo de pasos
const step0Container = document.getElementById("step0")
const step1Container = document.getElementById("languageInputContainer")
const step2Container = document.getElementById("sublanguagesContainer")
const step3Container = document.getElementById("levelSelection")
const step4Container = document.getElementById("teach")
const teachFM = document.getElementById("teachType")
const step5Container = document.getElementById("priceInputContainer")
const nextStepButton = document.getElementById("nextStep")
let hasSublanguages = false
const submitButton = document.getElementById("submit")
let step = 0
let useOfLanguage = ""

// Función para actualizar useOfLanguage
function updateUseOfLanguage() {
  useOfLanguage = use.value
  console.log("useOfLanguage actualizado:", useOfLanguage)
}

// Función para obtener parámetros de la URL
function getUrlParameter(name) {
  name = name.replace(/[[]/, "\\[").replace(/[\]]/, "\\]")
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)")
  var results = regex.exec(location.search)
  return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "))
}

export function updateStepView() {
  step0Container.style.display = "none"
  step1Container.style.display = "none"
  step2Container.style.display = "none"
  step3Container.style.display = "none"
  step4Container.style.display = "none"
  step5Container.style.display = "none"
  submitButton.style.display = "none"
  nextStepButton.style.display = "inline-block" // Valor por defecto
  nextStepButton.disabled = false

  console.log("Current step:", step)
  console.log("useOfLanguage:", useOfLanguage)

  switch (step) {
    case 0:
      step0Container.style.display = "block"
      use.addEventListener("change", () => {
        updateUseOfLanguage()
        if (useOfLanguage === "know" || useOfLanguage === "learn") {
          nextStepButton.disabled = false
        } else {
          nextStepButton.disabled = true
        }
      })

      if (useOfLanguage === "know" || useOfLanguage === "learn") {
        nextStepButton.disabled = false
      } else {
        nextStepButton.disabled = true
      }
      break

    case 1:
      step1Container.style.display = "block"
      const languageInput = document.getElementById("languageInput")

      nextStepButton.disabled = languageInput.value.trim() === ""

      languageInput.addEventListener("input", () => {
        nextStepButton.disabled = languageInput.value.trim() === ""
      })
      break

    case 2:
      step2Container.style.display = hasSublanguages ? "block" : "none"
      break

    case 3:
      step3Container.style.display = "block"
      console.log("Case 3 - useOfLanguage:", useOfLanguage)
      if (useOfLanguage === "learn") {
        nextStepButton.style.display = "none"
        submitButton.style.display = "inline-block"
      } else {
        nextStepButton.disabled = false
      }
      break

    case 4:
      step4Container.style.display = "block"
      nextStepButton.disabled = true
      console.log("Case 4 - teachFM value:", teachFM.value)
      if (teachFM.value === "tfm") {
        nextStepButton.disabled = false
        nextStepButton.style.display = "inline-block"
        submitButton.style.display = "none"
      } else if (teachFM.value === "e" || teachFM.value === "jc") {
        nextStepButton.style.display = "none"
        submitButton.style.display = "inline-block"
      }
      break

    case 5:
      if (teachFM.value === "tfm") {
        step5Container.style.display = "block"
        nextStepButton.disabled = true
        submitButton.style.display = "inline-block"
        nextStepButton.style.display = "none"
      }
      break

    default:
      step0Container.style.display = "block"
      break
  }

  // Modificar la lógica de visualización de botones
  if (step >= 3 && useOfLanguage === "learn") {
    nextStepButton.style.display = "none"
    nextStepButton.disabled = true
    submitButton.style.display = "inline-block"
  }

  if ((teachFM.value === "e" || teachFM.value === "jc") && step === 4) {
    nextStepButton.style.display = "none"
    submitButton.style.display = "inline-block"
  }

  /* if (step < 5) {
    nextStepButton.style.display = "inline-block"
  } */

  if (step === 3) {
    console.log("Step 3 check - useOfLanguage:", useOfLanguage)
  }
}

teachFM.addEventListener("change", () => {
  if (teachFM.value === "e" || teachFM.value === "jc") {
    nextStepButton.disabled = true
    nextStepButton.style.display = "none"
    submitButton.style.display = "inline-block"
  } else if (teachFM.value === "tfm") {
    nextStepButton.disabled = false
    nextStepButton.style.display = "inline-block"
    submitButton.style.display = "none"
  }
  updateStepView()
})

async function generateCheckboxes(sublanguages, containerId = "sublanguagesList") {
  const div = document.getElementById(containerId)

  div.innerHTML = ""
  sublanguages.forEach((item) => {
    const checkbox = document.createElement("input")
    checkbox.type = "checkbox"
    checkbox.value = item.Id
    checkbox.id = `checkbox-${item.Id}`
    checkbox.name = "sublanguages[]"

    const label = document.createElement("label")
    label.htmlFor = `checkbox-${item.Id}`
    // Mostrar "Nombre (código)" como pidió el usuario
    label.textContent = `${item.Name} (${item.Id})`

    // Evento para limitar la selección a 7 checkboxes
    checkbox.addEventListener("change", () => {
      const checkboxes = div.querySelectorAll("input[type='checkbox']")
      const checkedCount = Array.from(checkboxes).filter((cb) => cb.checked).length
      checkboxes.forEach((cb) => (cb.disabled = checkedCount >= 7 && !cb.checked))
    })

    div.appendChild(checkbox)
    div.appendChild(label)
    div.appendChild(document.createElement("br"))
  })
}

export async function loadSublanguages(langId) {
  if (!langId) {
    console.warn("No se proporcionó un ID de idioma.")
    return false
  }

  try {
    const response = await fetch(`api.php?lang=${encodeURIComponent(langId)}`)
    if (!response.ok) {
      throw new Error(`Error en la petición: ${response.statusText}`)
    }

    const data = await response.json()
    console.log("Datos recibidos:", data)

    const sublanguages = data.sublanguages || []
    hasSublanguages = sublanguages.length > 0

    generateCheckboxes(sublanguages)

    // document.getElementById("sublanguagesContainer").style.display = hasSublanguages ? "block" : "none"

    return hasSublanguages
  } catch (error) {
    console.error("Error cargando sublenguajes:", error)
    generateCheckboxes([])
    hasSublanguages = false
    return false
  }
}

window.onload = () => {
  // Obtener parámetros de la URL
  const langParam = getUrlParameter("lang")
  const useParam = getUrlParameter("use")

  if (langParam && useParam) {
    // Si los parámetros están presentes en la URL, usarlos directamente
    document.getElementById("languageInput").value = langParam
    use.value = useParam
    useOfLanguage = useParam
    step = 2 // Saltar directamente al paso 2
  } else {
    // Si no hay parámetros en la URL, inicializar normalmente
    updateUseOfLanguage()
  }

  document.getElementById("nextStep").addEventListener("click", async () => {
    if (step === 1) {
      const selectedLang = document.getElementById("languageInput").value.trim()
      if (selectedLang) {
        hasSublanguages = await loadSublanguages(selectedLang)
      }

      if (!hasSublanguages) {
        step += 2
      } else {
        step += 1
      }
    } else if (step < 5) {
      step++
    }

    console.log("Paso actualizado a:", step)
    updateStepView()
  })

  document.getElementById("prevStep").addEventListener("click", () => {
    if (step === 3 && !hasSublanguages) {
      step -= 2
    } else if (step > 0) {
      step--
    }
    console.log(step)
    updateStepView()
  })

  updateStepView()

  // STEP 1: Búsqueda de idiomas con autocompletado
  const langInput = document.getElementById("languageInput")

  langInput.addEventListener("input", async () => {
    const searchText = langInput.value.trim()
    if (!searchText) return

    try {
      const response = await fetch(`api.php?searchText=${encodeURIComponent(searchText)}`)
      if (!response.ok) throw new Error("Error en la petición")

      const data = await response.json()
      const datalist = document.getElementById("languageOptions")
      datalist.innerHTML = ""

      data.languages.forEach((lang) => {
        const option = document.createElement("option")
        option.value = lang.Id
        option.textContent = `${lang.Name} (${lang.Id})`
        datalist.appendChild(option)
      })
    } catch (error) {
      console.error("Error buscando idiomas:", error)
    }
  })

  // STEP 2: Cargar sublenguajes dinámicamente al seleccionar un idioma
  langInput.addEventListener("change", async () => {
    const selectedLang = langInput.value.trim()
    if (!selectedLang) return

    try {
      const response = await fetch(`api.php?lang=${encodeURIComponent(selectedLang)}`)
      if (!response.ok) throw new Error(`Error en la petición: ${response.statusText}`)

      const data = await response.json()
      hasSublanguages = data.sublanguages?.length > 0
      // console.log("hasSublanguages definido en change:", hasSublanguages);
    } catch (error) {
      console.error("Error cargando sublenguajes:", error)
      hasSublanguages = false
    }
  })
}

