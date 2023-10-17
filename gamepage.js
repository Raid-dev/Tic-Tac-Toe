const plates = document.querySelectorAll(".plates")
const promptt = document.querySelector(".prompt")
const line = document.querySelector(".line")

var p1_wins = parseInt(document.querySelector(".p1-wins").innerHTML)
var p2_wins = parseInt(document.querySelector(".p2-wins").innerHTML)

var total_p1_wins = document.querySelector(".total-p1-wins").innerHTML
var total_p2_wins = document.querySelector(".total-p2-wins").innerHTML

P1totalgames = eval(P1totalgames)
P2totalgames = eval(P2totalgames)
setTimeout(check4feedback(P1totalgames, P2totalgames), 20000);

var whose_turn = ""
var plate_id = ""
var selected_plates = []
var count = 0
var game_status = ""

start_game()

function start_game() {
    game_status = "new game"

    p1_wins = parseInt(document.querySelector(".p1-wins").innerHTML)
    p2_wins = parseInt(document.querySelector(".p2-wins").innerHTML)

    var random_number = Math.floor(2*Math.random() + 1)

    switch (random_number) {
        case 1:
            whose_turn = "p1"
            promptt.innerHTML = P1name + " starts"
            break
        case 2:
            whose_turn = "p2"
            promptt.innerHTML = P2name + " starts"
            break
    };
}

plates.forEach(plate => {
    plate.onclick = function() {
        plate_id = plate.className[plate.className.length - 1]

        if(!selected_plates[plate_id - 1] && game_status == "new game") {

            if (whose_turn == "p1") {
                selected_plates[plate_id - 1] = "x"
                document.querySelector(".x-" + plate_id).style.display = "inline-block"
                whose_turn = "p2"
                promptt.innerHTML = P2name + "'s turn"
            }
            else if (whose_turn == "p2") {
                selected_plates[plate_id - 1] = "o"
                document.querySelector(".o-" + plate_id).style.display = "inline-block"
                whose_turn = "p1"
                promptt.innerHTML = P1name + "'s turn"
            }
            
            check_status(selected_plates)
        }
    }
})

function check_status(arr) {
    if((arr[0] == "x" && arr[1] == "x" && arr[2] == "x") || (arr[0] == "o" && arr[1] == "o" && arr[2] == "o"))
        line.className += "-1"
    else if((arr[3] == "x" && arr[4] == "x" && arr[5] == "x") || (arr[3] == "o" && arr[4] == "o" && arr[5] == "o"))
        line.className += "-2"
    else if((arr[6] == "x" && arr[7] == "x" && arr[8] == "x") || (arr[6] == "o" && arr[7] == "o" && arr[8] == "o"))
        line.className += "-3"
    else if((arr[0] == "x" && arr[3] == "x" && arr[6] == "x") || (arr[0] == "o" && arr[3] == "o" && arr[6] == "o"))
        line.className += "-4"
    else if((arr[1] == "x" && arr[4] == "x" && arr[7] == "x") || (arr[1] == "o" && arr[4] == "o" && arr[7] == "o"))
        line.className += "-5"
    else if((arr[2] == "x" && arr[5] == "x" && arr[8] == "x") || (arr[2] == "o" && arr[5] == "o" && arr[8] == "o"))
        line.className += "-6"
    else if((arr[0] == "x" && arr[4] == "x" && arr[8] == "x") || (arr[0] == "o" && arr[4] == "o" && arr[8] == "o"))
        line.className += "-7"
    else if((arr[2] == "x" && arr[4] == "x" && arr[6] == "x") || (arr[2] == "o" && arr[4] == "o" && arr[6] == "o"))
        line.className += "-8"

    if((arr[0] == "x" && arr[1] == "x" && arr[2] == "x") || (arr[3] == "x" && arr[4] == "x" && arr[5] == "x") || (arr[6] == "x" && arr[7] == "x" && arr[8] == "x") || (arr[0] == "x" && arr[3] == "x" && arr[6] == "x") || (arr[1] == "x" && arr[4] == "x" && arr[7] == "x") || (arr[2] == "x" && arr[5] == "x" && arr[8] == "x") || (arr[0] == "x" && arr[4] == "x" && arr[8] == "x") || (arr[2] == "x" && arr[4] == "x" && arr[6] == "x")) {
        game_status = "ended"
        promptt.innerHTML = P1name + " WON !"
        line.style.display = "inline-block"
        total_p1_wins = eval(total_p1_wins + "+1")
        document.querySelector(".p1-wins").innerHTML = p1_wins + 1 + "(" + total_p1_wins + ")"
        $(document).ready(function(){
            $.ajax({
                url:"update_data.php",
                method:'POST',
                data: {
                    item: "P1 Won",
                },
            });
        });
        P1totalgames = P1totalgames + 1
        P2totalgames = P2totalgames + 1

        setTimeout(check4feedback(P1totalgames, P2totalgames), 2000)        
        
        setTimeout(reset_game, 3000)
    }
    else if((arr[0] == "o" && arr[1] == "o" && arr[2] == "o") || (arr[3] == "o" && arr[4] == "o" && arr[5] == "o") || (arr[6] == "o" && arr[7] == "o" && arr[8] == "o") || (arr[0] == "o" && arr[3] == "o" && arr[6] == "o") || (arr[1] == "o" && arr[4] == "o" && arr[7] == "o") || (arr[2] == "o" && arr[5] == "o" && arr[8] == "o") || (arr[0] == "o" && arr[4] == "o" && arr[8] == "o") || (arr[2] == "o" && arr[4] == "o" && arr[6] == "o")) {
        game_status = "ended"
        promptt.innerHTML = P2name + " WON !"
        line.style.display = "inline-block"
        total_p2_wins = eval(total_p2_wins + "+1")
        document.querySelector(".p2-wins").innerHTML = p2_wins + 1 + "(" + total_p2_wins + ")"
        $(document).ready(function(){
            $.ajax({
                url:"update_data.php",
                method:'POST',
                data: {
                    item: "P2 Won",
                },
            });
        });

        P1totalgames = P1totalgames + 1
        P2totalgames = P2totalgames + 1

        setTimeout(check4feedback(P1totalgames, P2totalgames), 2000)

        setTimeout(reset_game, 3000)
    }
    else {
        count = 0
        for (let i = 0; i < 9; i++) {
            (!arr[i]) ? count++ : ""    
        }    
        if (count == 0) {
            game_status = "ended"
            promptt.innerHTML = "DRAW"
            $(document).ready(function(){
                $.ajax({
                    url:"update_data.php",
                    method:'POST',
                    data: {
                        item: "Draw",
                    },
                });
            });
            P1totalgames = P1totalgames + 1
            P2totalgames = P2totalgames + 1
    
            setTimeout(check4feedback(P1totalgames, P2totalgames), 2000)

            setTimeout(reset_game, 3000)
        }
    }
}

function check4feedback(P1, P2) {
    const feedback_window = document.querySelector('.feedback-window')
    
    const p1_feedback = document.getElementById('p1-feedback')
    const p2_feedback = document.getElementById('p2-feedback')

    const send_feedback_btn = document.querySelector('.send-feedback-btn')

    send_feedback_btn.onclick = function () {
        feedback_window.style.display = "none"
    }

    if (P1 == 2 && P2 == 2) {
        feedback_window.style.display = "inline-block"
        p1_feedback.style.display = "inline-block" 
        p2_feedback.style.display = "inline-block" 
    }
} 

function reset_game() {
    for (let i = 0; i < 9; i++) {
        if(selected_plates[i] == "x") {
            document.querySelector(".x-" + (i+1)).style.display = "none"
            selected_plates[i] = ""
        }
        else if(selected_plates[i] == "o") {
            document.querySelector(".o-" + (i+1)).style.display = "none"
            selected_plates[i] = ""
        }
    }               
    
    line.className = "line line"
    line.style.display = "none" 
    
    game_status = "new game"
    
    start_game()
}

window.addEventListener('unload', () => {
    $(document).ready(function(){
        $.ajax({
            url:"update_data.php",
            method:'POST',
            data: {
                item: "Reset",
            },
        });
    });
});

if ( window.history.replaceState ) {
    window.history.replaceState( null, null, window.location.href );
}

// //////////////////////////////////////////////////////////////////////

