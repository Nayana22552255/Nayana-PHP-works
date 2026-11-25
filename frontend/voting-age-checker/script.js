let ageInput=document.getElementById("age");

let checkBtn=document.getElementById("checkBtn");

let result=document.getElementById("result");


checkBtn.addEventListener("click", function()
{
    let age=Number(ageInput.value);

    if(ageInput.value.trim() === "")
    {
        result.textContent="Please enter your age";

        result.style.color="orange";
        return;
    }

    if(!Number.isInteger(age) || age < 0)
    {
        result.textContent="Please enter a valid age";
        result.style.color="orange";
        return;
    }

    if(age >= 18)
    {
        result.textContent="You are eligible to vote!";
        result.style.color="green";
    }
    else
    {
        result.textContent="You are not eligible to vote!";
        result.style.color="red"
    }
});