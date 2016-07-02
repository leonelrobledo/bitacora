<?php
// This file is part of Moodle - http://moodle.org/

// it under//
// Moo the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Prints a particular instance of bitacora
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod_bitacora
 * @copyright  2015 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Replace bitacora with the name of your module and remove this line.

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // ... bitacora instance ID - it should be named as the first character of the module.

if ($id) {
    $cm         = get_coursemodule_from_id('bitacora', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $bitacora  = $DB->get_record('bitacora', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $bitacora  = $DB->get_record('bitacora', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $bitacora->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('bitacora', $bitacora->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$event = \mod_bitacora\event\course_module_viewed::create(array(
    'objectid' => $PAGE->cm->instance,
    'context' => $PAGE->context,
));
$event->add_record_snapshot('course', $PAGE->course);
$event->add_record_snapshot($PAGE->cm->modname, $bitacora);
$event->trigger();

// Print the page header.

$PAGE->set_url('/mod/bitacora/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($bitacora->name));
$PAGE->set_heading(format_string($course->fullname));

/*
 * Other things you may want to set - remove if not needed.
 * $PAGE->set_cacheable(false);
 * $PAGE->set_focuscontrol('some-html-id');
 * $PAGE->add_body_class('bitacora-'.$somevar);
 */

// Output starts here.
echo $OUTPUT->header();

echo "<style>
body{
background-color:white;

}

.container { 
  display: table;
  background-color:black;
  
  
  position: relative;
  
 
   align-self: center;
} 
.container div { 
  display: table-cell; padding: 5px; 
  background-color:white;
  border:10px solid black;
  width:50%;
}


.container2 {
  display: flex;
  flex-direction: column;
}
.centered {
  align-self: center;
}

</style>";
// Conditions to show the intro can change to look for own settings or whatever.
if ($bitacora->intro) {
    echo $OUTPUT->box(format_module_intro('bitacora', $bitacora, $cm->id), 'generalbox mod_introbox', 'bitacoraintro');
}

// Replace the following lines with you own code.
echo $OUTPUT->heading('Ver Proyectos'); // Nombre

$connection = mysqli_connect("localhost", "root", "123", "proyectos");  // COnexion
echo "<hr>";

if (isset($_GET['data'])) // Si agregas nota
{
    // Inserta
    $insert=$connection->query("INSERT INTO  
        `anotaciones`(`idanotaciones`, `Data`, `Porcentaje`, `proyectos_idproyectos`) 
        VALUES (null,'".$_GET['data']."',".$_GET['porcentaje'].",".$_GET['variable'].")");
    // Cambia Nombre
    $update=$connection->query("UPDATE `proyectos` SET `Nombre`='".$_GET['nombre']."' WHERE idproyectos=".$_GET['variable']."");
// Si alguno funciona redirige
    if($insert==1 or $update==TRUE)
    {
      //esto redirige
   echo "<script type='text/javascript'>window.location.href='http://localhost:8080/moodle/mod/bitacora/view.php?id=8&variable=".$_GET['variable']."'</script>";

    window.location.replace("http://localhost:8080/moodle/mod/bitacora/view.php?id=8&variable=".$_GET['variable']."");
    }
}

// Esto muestra la info de un proyecto el variable se envia en el link mas abajo se explica
elseif(isset($_GET['variable']))
{
$query=$connection->query("SELECT * FROM `proyectos`,`anotaciones` where idproyectos=proyectos_idproyectos and idproyectos=".$_GET['variable']."");


$resultado = $connection->query("SELECT * FROM `proyectos` where idproyectos=".$_GET['variable']."");
$row = $resultado->fetch_assoc();
 echo"  <div> <form action='http://localhost:8080/moodle/mod/bitacora/view.php?id=8&variable=".$row['idproyectos']."' method='GET'>";


echo "<input type='text' class='form-control' name='nombre' id='porcentaje' value='".$row['Nombre']."'>";





$query=$connection->query("SELECT * FROM `proyectos`,`anotaciones` where idproyectos=proyectos_idproyectos and idproyectos=".$_GET['variable']."");

    while ($row = $query->fetch_assoc())
    {
      echo 
      "<div>
      <table  '>
      <tr>
      <td>
      <li><h3>". $row['Data']." </h3></li>
      <tr>
      <td>
      <li><h3>Avance de un ". $row['Porcentaje']."%</h3></li>
      <td/>
      <td>
      <tr>
      </td>
      </tr>
      <tr>

      </table></div>";
    }

    echo 
    "<div>
    <form action='http://localhost:8080/moodle/mod/bitacora/view.php?id=8&variable=".$row['idproyectos']."' method='GET'>
    <input type='hidden' name='id' value=8>
    <input type='hidden' name='variable' value=".$_GET['variable'].">
  <div class='form-group'>
    <label for='exampleInputEmail1'><h3>Nuevos avances</h3></label>
    <input type='text' class='form-control' name='data' id='data' placeholder='Nuevos avances'>
  </div>
  <div class='form-group'>
    <label for='exampleInputPassword1'>Avance en porcentaje %</label>
    <input type='number' class='form-control' name='porcentaje' id='porcentaje' placeholder='0%'>
  </div>

  <button type='submit'  class='btn btn-default'>Cargar</button>
</form></div>
    ";

}
// Crea nuevo proyecto y despues redirige

elseif(isset($_GET['proyecto']))
{
    
    $insert=$connection->query("INSERT INTO `proyectos`(`idproyectos`, `Nombre`) VALUES (null,'".$_GET['proyect']."')");

    if($insert==1 )
    {
   echo "<script type='text/javascript'>window.location.href='http://localhost:8080/moodle/mod/bitacora/view.php?id=8'</script>";

    window.location.replace("http://localhost:8080/moodle/mod/bitacora/view.php?id=8");
    }
}

// si no se cumple nada de lo anterior muestra la lista de todos los proyectos
else
{

$query=$connection->query("SELECT * FROM `proyectos`");


    while ($row = $query->fetch_assoc())
    {
      // eso se envia omo un get  en la url .$row['idproyectos']
      echo "

  
      <span>      
      <table >
      <tr>
      <td> <li><h3><a href='http://localhost:8080/moodle/mod/bitacora/view.php?id=8&variable=".$row['idproyectos']."'>". $row['Nombre']."</a></h3></li><td/>
      </tr>
      <tr>

      </table>
      
      </span>";

      

       
    }



    // formualrio para crear nuevo proyecto
      echo 
    "

 
      <span>    <form action='http://localhost:8080/moodle/mod/bitacora/view.php?id=8' method='GET'>
    <input type='hidden' name='id' value=8>
    <input type='hidden' name='proyecto' value=1>

    <label for='exampleInputEmail1'><h3>Nuevos Proyecto</h3></label>
    <input type='text' class='form-control' name='proyect' id='data' placeholder='Nombre'>



  <button type='submit'  class='btn btn-default'>Cargar</button>
</form>
      </span>
      

  

    ";
  

 
  


}
// Finish the page.
echo $OUTPUT->footer();
