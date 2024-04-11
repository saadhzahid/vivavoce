

function addQuestionstobox() {


    console.log("ADDING QUESTIONS TO BOX")

    var initialQuestionField = document.getElementById('id_manualquestions');
    var allQuestionsField = document.getElementById('id_allquestions');
    var displayQuestionsField = document.getElementById('id_displayquestions');
    var addButton = document.getElementById('id_addquestion');    
    var newQuestion = initialQuestionField.value.trim();






    if (newQuestion !== '') {

        //cleans up question so that it contains a question mark and capital letter
        var hasQuestionMark = /[?]$/.test(newQuestion);
        if (!hasQuestionMark) {
            newQuestion += '?';
        }
        newQuestion = newQuestion.charAt(0).toUpperCase() + newQuestion.slice(1);
    

        if (displayQuestionsField.value.trim() !== '') {
            displayQuestionsField.value += "\n"; // Add a newline if not the first question
        }
        displayQuestionsField.value += "- " + newQuestion;
        initialQuestionField.value = ''; // Clear the initial question field

    }
}


function toggleTextareaEditable(button) {



    var displayQuestionsTextarea = document.getElementById('id_displayquestions');

    displayQuestionsTextarea.readOnly = !displayQuestionsTextarea.readOnly;

    // If not readonly, focus the textarea
    if (!displayQuestionsTextarea.readOnly) {
        displayQuestionsTextarea.focus();
    }

    // Toggle button text and color
    if (displayQuestionsTextarea.readOnly) {
        button.innerHTML = "Edit";
    } else {
        button.innerHTML = "Unedit";
    }
}

function toggleTextboxes(radio) {

    // Radio button select


    var manualQuestionsTextarea = document.getElementById('id_manualquestions'); 

    var addbutton = document.getElementById('id_addquestion'); // Replace 'id_manualquestions' with the actual ID of your textarea element

    var editbutton = document.getElementById('id_toggleedit'); // Replace 'id_manualquestions' with the actual ID of your textarea element




    if (radio.value === 'manual') {
        manualQuestionsTextarea.disabled = false;
        addbutton.disabled = false;
        editbutton.disabled = false;

    } else {
        manualQuestionsTextarea.disabled = true;
        addbutton.disabled = true;
        editbutton.disabled = false;


    }
}

async function generatequestions() {

    var displayQuestionsField = document.getElementById('id_displayquestions');


    var goalsObjectives = document.getElementById('id_goals_objectives').value;
    var deliverables = document.getElementById('id_deliverables').value;
    var constraints = document.getElementById('id_constraints').value;
    var otherInfo = document.getElementById('id_other_info').value;

    
    const userContext = `${goalsObjectives} ${deliverables} ${constraints} ${otherInfo}`;

    const userPrompt = 'With the following context, generate me stricly 10 Viva Voce questions and nothing else, tailored to this specific project using the context (display only questions and have all questions output beginning with - and ending in ?):';

    try {
        const response = await fetch('/moodle/mod/vivavoce/gptgeneration.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ context: userContext, prompt: userPrompt }) // Sending both context and prompt to PHP
        });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const data = await response.json(); // Expecting JSON response
        console.log(data.response);

        displayQuestionsField.value = '';
        displayQuestionsField.value +=  data.response;






    } catch (error) {
        console.error('Error fetching response from PHP:', error);
    }
}