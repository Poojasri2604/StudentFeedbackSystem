const subjects = [
    { id: "hs3152", value: "Engineering Mathematics" },
    { id: "ma3151", value: "Physics" },
    { id: "ph3151", value: "Chemistry" },
    { id: "ge3151", value: "Environmental Science" },
    { id: "cs3251", value: "Data Structures" }
];

function filterSubjects() {
    const input = document.getElementById("subjectInput");
    const filter = input.value.toLowerCase().trim();
    const dropdown = document.getElementById("subjectDropdown");
    
    dropdown.innerHTML = ""; // Clear previous results

    if (filter) {
        let matchCount = 0;

        subjects.forEach(subject => {
            if (subject.value.toLowerCase().includes(filter) || subject.id.includes(filter)) {
                const div = document.createElement("div");
                div.textContent = `${subject.value} (${subject.id})`;  
                div.classList.add("dropdown-item");
                div.onclick = () => selectSubject(subject.id, subject.value);
                dropdown.appendChild(div);
                matchCount++;
            }
        });

        dropdown.style.display = matchCount > 0 ? "block" : "none";
    } else {
        dropdown.style.display = "none";
    }
}

function selectSubject(id, value) {
    document.getElementById("subjectInput").value = value;
    document.getElementById("subjectId").value = id;
    document.getElementById("subjectDropdown").style.display = "none";
}

// Hide dropdown when clicking outside
document.addEventListener("click", function(event) {
    const dropdown = document.getElementById("subjectDropdown");
    const input = document.getElementById("subjectInput");

    if (!dropdown.contains(event.target) && event.target !== input) {
        dropdown.style.display = "none";
    }
});

// Initialize event listener
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("subjectInput").addEventListener("input", filterSubjects);
});
