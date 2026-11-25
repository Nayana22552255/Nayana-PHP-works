let taskInput=document.getElementById("taskInput");

let addBtn=document.getElementById("addBtn");

let taskList=document.getElementById("taskList");

function addTask()
{
    let task=taskInput.value;           //get the task enterd by the user

    if(task.trim() === "")
    {
        alert("Please enter a task");
        return
    }

    let li=document.createElement("li");        //create a new list item

    let span = document.createElement("span");

    //add task text
    span.textContent=task;

    let editBtn=document.createElement("button");

    editBtn.textContent="Edit";

    editBtn.className="editBtn";

    editBtn.addEventListener("click",function()
    {
            let updateTask=prompt("Edit your Task:", span.textContent);

            if(updateTask !== null && updateTask.trim() !== "")
            {
                span.textContent=updateTask.trim();
            }
    })

    let deleteBtn=document.createElement("button");

    deleteBtn.textContent="Delete";

    deleteBtn.className="deleteBtn";

    deleteBtn.addEventListener("click",function()
   {
    li.remove();
   });

   let buttonGroup=document.createElement("div");
   buttonGroup.className="buttonGroup";
   buttonGroup.appendChild(editBtn);
   buttonGroup.appendChild(deleteBtn);

   li.appendChild(span);
   li.appendChild(buttonGroup);
  

    taskList.appendChild(li);               //display the task on the webpage

    taskInput.value= "";       //clear the input box   

}

addBtn.addEventListener("click",addTask);
