import javax.swing.JButton;

@SuppressWarnings("serial")
public class Card extends JButton{ //WANT TO BE PANES LATER
    private int id;
    private boolean matched = false;


    public void setId(int id){ //sets the id of the cards
        this.id = id;
    }

    public int getId(){  //gets the set id
        return this.id;
    }


    public void setMatched(boolean matched){
        this.matched = matched;
    }

    public boolean getMatched(){
        return this.matched;
    }
}
