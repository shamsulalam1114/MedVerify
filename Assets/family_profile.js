
document.addEventListener("DOMContentLoaded", function() {
    const addMemberBtn = document.getElementById("addMemberBtn");
    const clearFormBtn = document.getElementById("clearFormBtn");
    const memberNameInput = document.getElementById("memberName");
    const memberRelationInput = document.getElementById("memberRelation");
    const memberAgeInput = document.getElementById("memberAge");
    const memberBloodInput = document.getElementById("memberBlood");
    const familyMembersTable = document.getElementById("familyMembersTable");
    const familyMembersCount = document.getElementById("familyMembersCount");

    let memberIdCounter = 2000;
    let totalMembers = 3;

    addMemberBtn.addEventListener("click", function() {
        
        let memberName = memberNameInput.value.trim();
        let memberRelation = memberRelationInput.value.trim();
        let memberAge = memberAgeInput.value.trim();
        let memberBlood = memberBloodInput.value.trim();

        if (memberName === "") {
            alert("Name field cannot be empty!");
            return;
        }

        if (memberRelation === "") {
            alert("Relationship field cannot be empty!");
            return;
        }

        if (memberAge === "") {
            alert("Age field cannot be empty!");
            return;
        }

        if (isNaN(memberAge)) {
            alert("Age must be a number!");
            return;
        }

        let age = Number(memberAge);

        if (age < 0 || age > 120) {
            alert("Age must be between 0 and 120!");
            return;
        }

        if (memberBlood === "") {
            alert("Blood group field cannot be empty!");
            return;
        }

        const newRow = document.createElement("tr");

        const idCell = document.createElement("td");
        idCell.innerHTML = memberIdCounter;

        const nameCell = document.createElement("td");
        nameCell.innerHTML = memberName;

        const relationCell = document.createElement("td");
        relationCell.innerHTML = memberRelation;

        const ageCell = document.createElement("td");
        ageCell.innerHTML = age;

        const bloodCell = document.createElement("td");
        bloodCell.innerHTML = memberBlood;

        const actionCell = document.createElement("td");
        actionCell.innerHTML = '<a href="#">View Profile</a>';

        newRow.appendChild(idCell);
        newRow.appendChild(nameCell);
        newRow.appendChild(relationCell);
        newRow.appendChild(ageCell);
        newRow.appendChild(bloodCell);
        newRow.appendChild(actionCell);

        familyMembersTable.appendChild(newRow);

        memberIdCounter = memberIdCounter + 1;
        totalMembers = totalMembers + 1;
        familyMembersCount.innerHTML = totalMembers;

        alert("Family member added successfully!");

        memberNameInput.value = "";
        memberRelationInput.value = "";
        memberAgeInput.value = "";
        memberBloodInput.value = "";
    });

    clearFormBtn.addEventListener("click", function() {
        memberNameInput.value = "";
        memberRelationInput.value = "";
        memberAgeInput.value = "";
        memberBloodInput.value = "";
    });
});


