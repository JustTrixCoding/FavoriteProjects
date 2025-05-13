import javax.swing.JFrame;
import javax.swing.JOptionPane;
import javax.swing.Timer;
import java.awt.*;
import java.awt.event.*;
import java.util.ArrayList;
import java.util.List;
import java.util.Collections;

@SuppressWarnings("serial") //idk what this does but i think i need it
public class Board extends JFrame{ //WANT TO MAKE BUTTON COMANDS PANE COMMANDS


    private List<Card> cards;
    private Card selectedCard;
    private Card c1;
    private Card c2;
    private Timer t;

    public Board(){

        int pairs = 10;
        List<Card> cardsList = new ArrayList<Card>();
        List<Integer> cardVals = new ArrayList<Integer>();

        for (int i = 0; i < pairs; i++){
            cardVals.add(i);
            cardVals.add(i);
        }
        Collections.shuffle(cardVals);

        for (int val : cardVals){
            Card c = new Card();
            c.setId(val);
            c.addActionListener(new ActionListener(){
                public void actionPerformed(ActionEvent ae){
                    selectedCard = c;
                    doTurn();
                }
            });
            cardsList.add(c);
        }
        this.cards = cardsList;
//*************** TIMER *********************
        t = new Timer(750, new ActionListener(){                  //this allows the computer to relax || Pauses betweem each guess
            public void actionPerformed(ActionEvent ae){
                checkCards();
            }
        });

        t.setRepeats(false);

//************** BOARD SETUP *****************
        Container pane = getContentPane();  //this will set up the board filled with buttons c
        pane.setLayout(new GridLayout(4, 5));
        for (Card c : cards){
            pane.add(c);
        }
        setTitle("Memory Match");
    } //end of board
//************** DO TURN *********************
    public void doTurn(){  //this allows me to "turn over" the button
        if (c1 == null && c2 == null){
            c1 = selectedCard;
            c1.setText(String.valueOf(c1.getId()));
        }

        if (c1 != null && c1 != selectedCard && c2 == null){
            c2 = selectedCard;
            c2.setText(String.valueOf(c2.getId()));
            t.start();

        }
    }
//**************** CHECKCARDS ****************
    public void checkCards(){
        if (c1.getId() == c2.getId()){//match condition
            c1.setEnabled(false); //disables the button
            c2.setEnabled(false);
            c1.setMatched(true); //flags the button as having been matched
            c2.setMatched(true);
            if (this.isGameWon()){
                JOptionPane.showMessageDialog(this, "You win!");
                System.exit(0);
            }
        }

        else{
            c1.setText(""); //'hides' text
            c2.setText("");
        }
        c1 = null; //reset c1 and c2
        c2 = null;
    }
//*************** ISGAMEWON ******************
    public boolean isGameWon(){ //checks if all cards are match, if so, send the end screen
        for(Card c: this.cards){
            if (c.getMatched() == false){
                return false;
            }
        }
        return true;
    }

}
