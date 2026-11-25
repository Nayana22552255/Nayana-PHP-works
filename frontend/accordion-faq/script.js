//select all qusetion button
let questions=document.querySelectorAll(".question");        //dot(.) means find elments whose class is question

// add a click event
questions.forEach(function(question)
{
    question.addEventListener("click",function()
    {
        //get selected answer
        let answer=question.nextElementSibling;
        //check whether the answer is already open
        let isOpen=answer.style.display === "block";
        // hide all other answers
        questions.forEach(function(item)
        {
            item.classList.remove("active");        //removes the active styling
            item.nextElementSibling.style.display="none";  //selects the answers immediately following the question button and hides the answer

        });

        if(!isOpen)
        {
            answer.style.display="block";
            question.classList.add("active");
        }
    });
});

//display the answers beloned to the clicked qusetions
// let answers=questions.nextElementSibling;

// if(answers.style.display === "block")
// {
//     answers.style.display="none";
// }
// else
// {
//     answers.style.display= "block";
//     questions.classList.add("active");
// }