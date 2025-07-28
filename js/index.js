const input = document.getElementById("inputTask");
const btnAdd = document.getElementById("btn-add");
const main = document.getElementById("main");
let count = 0;

function addtask(){
    const inputValue = input.value;

    if((inputValue !== null) && (inputValue !== undefined) && (inputValue !== "")){
        ++count;

        const newTask = `
        <div id="${count}" class="item">
            <div onclick="markTask(${count})" class="item-icon">
                <i id="icon_${count}" class="fa-regular fa-circle"></i>
            </div>
            <div onclick="markTask(${count})" class="item-text">${inputValue}</div>
            <div class="item-delete">
                <button onclick="deleteBtn(${count})" class="btn-delete"><i class="fa-solid fa-trash"></i></button>
            </div>
        </div>
        `;

        main.innerHTML += newTask;
        input.value = "";
        input.focus();
    }
}

function markTask(id){
    const item = document.getElementById(id);
    const classAdd = item.getAttribute("class");

    if(classAdd === "item"){
        item.classList.add("clicked");

        const icon = document.getElementById("icon_"+id);
        icon.classList.remove("fa-circle");
        icon.classList.add("fa-check-circle");

        item.parentNode.appendChild(item);
    }else{
        item.classList.remove("clicked");

        const icon = document.getElementById("icon_"+id);
        icon.classList.remove("fa-check-circle");
        icon.classList.add("fa-circle");
    }
}

function deleteBtn(id){
    const removeTask = document.getElementById(id);
    removeTask.remove();
}



input.addEventListener("keyup",(e)=>{
    if(e.keyCode === 13){
        e.preventDefault();
        btnAdd.click();
    }
})