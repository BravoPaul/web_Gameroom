

function estValide(button)
{
     return button.innerHTML.length == 0;
}

function setSymbol(btn, symbole)
{
     btn.innerHTML = symbole;
    //alert("adsfasdf");

}

function rechercherVainqueur(pions, joueurs, tour)
{
     if (pions[0].innerHTML == joueurs[tour] &&
         pions[1].innerHTML == joueurs[tour] &&
         pions[2].innerHTML == joueurs[tour])
      {
        pions[0].style.backgroundColor = "#9ACD32";
        pions[1].style.backgroundColor = "#9ACD32";
        pions[2].style.backgroundColor = "#9ACD32";
        return true;
      }

      if (pions[3].innerHTML == joueurs[tour] &&
          pions[4].innerHTML == joueurs[tour] &&
          pions[5].innerHTML == joueurs[tour])
      {
        pions[3].style.backgroundColor = "#9ACD32";
        pions[4].style.backgroundColor = "#9ACD32";
        pions[5].style.backgroundColor = "#9ACD32";
        return true;
      }

      if (pions[6].innerHTML == joueurs[tour] &&
          pions[7].innerHTML == joueurs[tour] &&
          pions[8].innerHTML == joueurs[tour])
      {
        pions[6].style.backgroundColor = "#9ACD32";
        pions[7].style.backgroundColor = "#9ACD32";
        pions[8].style.backgroundColor = "#9ACD32";
        return true;
      }

      if (pions[0].innerHTML == joueurs[tour] &&
          pions[3].innerHTML == joueurs[tour] &&
          pions[6].innerHTML == joueurs[tour])
      {
        pions[0].style.backgroundColor = "#9ACD32";
        pions[3].style.backgroundColor = "#9ACD32";
        pions[6].style.backgroundColor = "#9ACD32";
        return true;
      }

      if (pions[1].innerHTML == joueurs[tour] &&
          pions[4].innerHTML == joueurs[tour] &&
          pions[7].innerHTML == joueurs[tour])
      {
        pions[1].style.backgroundColor = "#9ACD32";
        pions[4].style.backgroundColor = "#9ACD32";
        pions[7].style.backgroundColor = "#9ACD32";
        return true;
      }

      if (pions[2].innerHTML == joueurs[tour] &&
          pions[5].innerHTML == joueurs[tour] &&
          pions[8].innerHTML == joueurs[tour])
      {
        pions[2].style.backgroundColor = "#9ACD32";
        pions[5].style.backgroundColor = "#9ACD32";
        pions[8].style.backgroundColor = "#9ACD32";
        return true;
      }

      if (pions[0].innerHTML == joueurs[tour] &&
          pions[4].innerHTML == joueurs[tour] &&
          pions[8].innerHTML == joueurs[tour])
      {
        pions[0].style.backgroundColor = "#9ACD32";
        pions[4].style.backgroundColor = "#9ACD32";
        pions[8].style.backgroundColor = "#9ACD32";
        return true;
      }

      if (pions[2].innerHTML == joueurs[tour] &&
          pions[4].innerHTML == joueurs[tour] &&
          pions[6].innerHTML == joueurs[tour])
      {
        pions[2].style.backgroundColor = "#9ACD32";
        pions[4].style.backgroundColor = "#9ACD32";
        pions[6].style.backgroundColor = "#9ACD32";
        return true;
      }
}

function matchNul(pions)
{
     for (var i = 0, len = pions.length; i < len; i++)
     {
         if (pions[i].innerHTML.length == 0)
              return false;
     }

     return true;
}

var Afficheur = function(element)
{
    var affichage = element;

    function setText(message)
    {
        affichage.innerHTML = message;
    }

    return {sendMessage : setText};
}


function resetJeu(pions)
{
    for (var i = 0, len = pions.length; i < len; i++)
    {
        pions[i].innerHTML = "";
        pions[i].style.backgroundColor = "#fff";
    }
}

function main()
{
     var pions = document.querySelectorAll("#Jeu button");
     var joueurs = ['X', 'O'];
     var tour = 0;
     var jeuEstFini = false;
    var afficheur = new Afficheur(document.querySelector("#StatutJeu"));
     afficheur.sendMessage("Le jeu peut commencer,c'est votre tour.");
     for (var i = 0, len = pions.length; i < len; i++)
     {

         pions[i].addEventListener("click", function()
         {
            if(wait==0) {
                if (jeuEstFini)
                    return;

                if (!estValide(this)) {
                    afficheur.sendMessage("Case occupée ! <br />Joueur " + joueurs[tour] + " c'est toujours à vous !");

                }
                else {

                    setSymbol(this, joueurs[0]);
                    wait = 1;
                    var pace_data = '{"type":"pace","adverse":"'+adverse_id+'","lastPace":"'+this.id+'"}';
                    ws.send(pace_data);

                    jeuEstFini = rechercherVainqueur(pions, joueurs, tour);

                    if (jeuEstFini) {
                        afficheur.sendMessage("you have won the game");
                        var win_data = '{"type":"win","adverse":"'+adverse_id+'"}';
                        ws.send(win_data);
                        if(confirm("you have won the game, do you want to play again?")){

                            resetJeu(pions);
                            jeuEstFini = false;

                        }

                        if(first==1){
                            wait = 1;
                        }
                        else{
                            wait = 0;
                        }
                        return;
                    }

                    if (matchNul(pions)) {
                        afficheur.sendMessage("Match Nul !");
                        return;
                    }

                    tour++;
                    tour = tour % 2;
                    afficheur.sendMessage("Joueur " + joueurs[tour] + " c'est à vous !");
                }

            }

         });
     }
}

function getReady()
{
    ready = 1;
    var ready_data = '{"type":"ready"}';
    ws.send(ready_data);
}

main();
