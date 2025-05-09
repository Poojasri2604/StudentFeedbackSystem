const facultyList = [
    { id: 1, value: "Dr. M.S. Thanabal" }, { id: 2, value: "Dr. S. Pushpalatha" },
    { id: 3, value: "Dr. A. Thomas Paul Roy" }, { id: 4, value: "Dr. D. Suresh" },
    { id: 5, value: "Dr. N. Dhanalakshmi" }, { id: 6, value: "Dr. S. Satheeshbabu" },
    { id: 7, value: "Dr. M. Buvana" }, { id: 8, value: "Dr. A. Sathya Sofia" }
];

function filterFaculty() {
    const input = document.getElementById("facultyInput");
    const filter = input.value.toLowerCase().trim();
    const dropdown = document.getElementById("facultyDropdown");
    
    dropdown.innerHTML = ""; // Clear previous results

    if (filter) {
        let matchCount = 0;

        facultyList.forEach(faculty => {
            if (faculty.value.toLowerCase().includes(filter)) {
                const div = document.createElement("div");
                div.textContent = faculty.value;
                div.classList.add("dropdown-item");
                div.onclick = () => selectFaculty(faculty.id, faculty.value);
                dropdown.appendChild(div);
                matchCount++;
            }
        });

        dropdown.style.display = matchCount > 0 ? "block" : "none";
    } else {
        dropdown.style.display = "none";
    }
}

function selectFaculty(id, value) {
    document.getElementById("facultyInput").value = value;
    document.getElementById("facultyDropdown").style.display = "none";
}

// Hide dropdown when clicking outside
document.addEventListener("click", function(event) {
    const dropdown = document.getElementById("facultyDropdown");
    const input = document.getElementById("facultyInput");

    if (!dropdown.contains(event.target) && event.target !== input) {
        dropdown.style.display = "none";
    }
});

// Initialize event listener
document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("facultyInput").addEventListener("input", filterFaculty);
});
