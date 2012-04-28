<?php
$key = "/etc/cvm/key";
$uri="openvz+ssh://root@cvm-vz.cryto.net/system?keyfile={$key}&no_verify=1";
echo ("Connecting to libvirt (URI:$uri)\n");
$conn=libvirt_connect($uri,false);
if ($conn==false)
{
	echo ("Libvirt last error: ".libvirt_get_last_error()."\n");
	exit;
}
else
{
	$hostname=libvirt_connect_get_hostname($conn);
	echo ("hostname:$hostname\n");
	$domains = libvirt_domain_get_counts($conn);
	echo ("Domain count: Active {$domains['active']},Inactive {$domains['inactive']}, Total {$domains['total']}\n");
	
	$domains=libvirt_list_domains($conn);
	foreach ($domains as $dom)
	{
		echo ("Name:\t".libvirt_domain_get_name($dom)."\n");
		echo("UUID:\t".libvirt_domain_get_uuid_string($dom)."\n");
		$dominfo=libvirt_domain_get_info($dom);
		print_r($dominfo);
	}
	
	echo("done");
}
?>
