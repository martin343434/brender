<?php	
	if (isset($_GET['orderby'])) {
		$orderby=$_GET['orderby'];
	}
	else {
		$orderby="client";
	}
	if (isset($_GET['benchmark'])) {
        	print "benchmark ALL idle";
                $query="select * from clients where status='idle'";
                $results=mysql_query($query);
                while ($row=mysql_fetch_object($results)){
                        $client=$row->client;
                	send_order("$client","benchmark","","75");
                        print "benchmark $client<br/>";
                }
	}
	if (isset($_GET['disable'])) {
		$disable=$_GET['disable'];
		if ($disable=="all") {
                        print "disable ALL";
                        $query="select * from clients where status='idle' or status='rendering'";
                        $results=mysql_query($query);
                        while ($row=mysql_fetch_object($results)){
                                $client=$row->client;
                                send_order("$client","disable","","5");
                                print "disable $client<br/>";
                        }
        }
        else {
			send_order($disable,"disable","","5");
            print "disable client : $disable";
		}
		$msg= "disabled $disable <a href=\"clients.php\">reload</a><br/>";
		sleep(1);
		$refresh="0;URL=index.php?view=clients&msg=disabled $disable";
	}
	if (isset($_GET['enable'])) {
		$enable=$_GET['enable'];
		if ($enable=="all") {
			print "enable ALL";
			$query="select * from clients where status='disabled'";
        		$results=mysql_query($query);
			while ($row=mysql_fetch_object($results)){
				$client=$row->client;
				send_order($client,"enable","","5");
				print "enable $client<br/>";
			}
		}
		else if ($enable=="force_all"){
			print "force enable ALL<br/>";
			$query="select * from clients";
        		$results=mysql_query($query);
			while ($row=mysql_fetch_object($results)){
				$client=$row->client;
				send_order($client,"enable","","5");
				print "enabled $client<br/>";
			}
		}
		else {
			send_order($enable,"enable","","5");
			header( 'Location: index.php' );
		}
		sleep(2);
		$refresh="0;URL=index.php?view=clients&msg=enabled $enable";
		$msg= "enabled $enable <a href=\"clients.php\">reload</a><br/>";
	}
	if (isset($_GET['refresh'])) {	
		checking_alive_clients();
	}
	if (isset($_GET['stop'])) {
		$stop=$_GET['stop'];
		$msg= "stopped $stop <a href=\"clients.php\">reload</a><br/>";
		send_order($stop,"stop","","1");
		sleep(2);
		$refresh="0;URL=index.php?view=clients&msg=stopped $stop";
	}

if (isset($_GET['msg'])) {
	print "$_GET[msg] <a href=\"clients.php\">reload</a><br/>";
}

#--------read---------
	if ($_SERVER["SCRIPT_NAME"]=="/web/clients.php") {
		$query="select * from clients order by $orderby";
	}
	else {
		$query="select * from clients where status<>'not running' order by $orderby";
	}
	$results=mysql_query($query);
	print "<h2>// <b>clients</b></h2>";
	print "$query<br/>";
	print "<table border=0>";
	print "<tr>
		<td bgcolor=cccccc width=120 height=30 align=center><b><a href=\"index.php?view=clients&orderby=client\">client name</a></b></td>
		<td bgcolor=cccccc width=12 height=30 align=center><b><a href=\"index.php?view=clients&orderby=client_priority\">rp</a></b></td>
		<td bgcolor=ccccce width=120 align=center><b> &nbsp; <a href=\"index.php?view=clients&orderby=status\">status</a> &nbsp; </b></td>
		<td bgcolor=cccccc width=500 align=center><b> &nbsp; rem &nbsp; </b></td>
		<td bgcolor=cccccc width=120 align=center><b> &nbsp; &nbsp; </td>
		<td bgcolor=cccccc width=120 align=center><b> workhour start </td>
		<td bgcolor=cccccc width=120 align=center><b> workhour end </td>
		<td bgcolor=cccccc align=center></td>
	</tr>";
	while ($row=mysql_fetch_object($results)){
		$client=$row->client;
		$status=$row->status;
		$rem=$row->rem;
		$speed=$row->speed;
		$machinetype=$row->machinetype;
		$client_priority=$row->client_priority;
		$working_hour_start=$row->working_hour_start;
		$working_hour_end=$row->working_hour_end;
		$speed=$row->speed;
		if ($status<>"disabled") {
			$dis="<a href=\"index.php?view=clients&disable=$client\">disable</a>";
			$bgcolor="#bcffa6";
		}
		if ($status=="disabled") {
			$dis="<a href=\"index.php?view=clients&enable=$client\">enable</a>";
			$bgcolor="#ffaa99";
		}
		if ($status=="rendering") {
			$bgcolor="#99ccff";
		}
		if ($status=="not running") {
			$dis="";
			$bgcolor="#ffcc99";
		}
		print "<tr>
			<td bgcolor=ddddcc align=center><a href=\"index.php?view=logs&log=$client\"><font size=3>$client</font></a> <font size=1>($machinetype)</font></td> 
			<td bgcolor=$bgcolor align=center><font size=1>$speed /</font> <a href=\"#\" onclick=\"javascript:window.open('clients_priority_popup.php?client=$client&client_priority=$client_priority','winam','width=200,height=25')\"><font size=1>$client_priority</font></a></td>
			<td bgcolor=$bgcolor align=center>$status</td>
			<td bgcolor=$bgcolor align=center>$rem</td>
			<td bgcolor=$bgcolor align=center>$dis</td>
			<td bgcolor=$bgcolor align=center>$working_hour_start</td>
			<td bgcolor=$bgcolor align=center>$working_hour_end</td>
			<td bgcolor=$bgcolor align=center><a href=\"index.php?view=clients&stop=$client\">x</a></td>
		</tr>";
	}
	print "</table>";
print "<a href=\"index.php?view=clients&benchmark=all\"><b class=\"ordre\">benchmark ALL</b></a> - ";
print "<a href=\"index.php?view=clients&enable=all\"><b class=\"ordre\">enable ALL</b></a> - ";
print "<a href=\"index.php?view=clients&disable=all\"><b class=\"ordre\">disable ALL</b></a> - ";
print "<a href=\"index.php?view=clients&refresh=1\"><b class=\"ordre\">refresh</b></a> - ";
print "<a href=\"index.php?view=clients&enable=force_all\"><b class=\"ordre\">force_all_enable</b></a>";
print "<p><hr><p>";
print "<p><p>";
?>

