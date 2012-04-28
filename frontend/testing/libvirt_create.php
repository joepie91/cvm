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
	$xml = "<domain type='openvz' id='101'>
  <name>101</name>
  <uuid>86c12009-e591-a159-6e9f-91d18b85ef78</uuid>
  <vcpu>2</vcpu>
  <os>
    <type>exe</type>
    <init>/sbin/init</init>
  </os>
  <devices>
    <filesystem type='template'>
      <source name='debian-6.0-x86_64'/>
      <target dir='/'/>
    </filesystem>
    <disk type='file' device='disk'>
        <driver name='tap' type='aio'/>
        <source file='/vz/storage/g1.img'/>
        <target dev='xvda'/>
    </disk>
    <interface type='bridge'>
      <mac address='00:18:51:5b:ea:bf'/>
      <source bridge='br0'/>
      <target dev='veth101.0'/>
    </interface>
  </devices>
</domain>";
	
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
