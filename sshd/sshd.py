from twisted.cred.portal import Portal
from twisted.conch.ssh.factory import SSHFactory
from twisted.internet import reactor
from twisted.conch.ssh.keys import Key
from twisted.conch.interfaces import IConchUser
from twisted.conch.avatar import ConchUser
from twisted.cred.checkers import AllowAnonymousAccess

with open('id_rsa.sshd') as privateBlobFile:
    privateBlob = privateBlobFile.read()
    privateKey = Key.fromString(data=privateBlob)

with open('id_rsa.sshd.pub') as publicBlobFile:
    publicBlob = publicBlobFile.read()
    publicKey = Key.fromString(data=publicBlob)

class StandardRealm(object):
    def requestAvatar(self, avatarId, mind, *interfaces):
        return IConchUser, ConchUser(), nothing

class CVMChecker:
    implements(ICredentialsChecker)
    credentialInterfaces = ICVMUser,

    def requestAvatarId(self, credentials):
        return defer.succeed("hai")
        
class ICVMUser(ICredentials):
    pass

factory = SSHFactory()
factory.privateKeys = {'ssh-rsa': privateKey}
factory.publicKeys = {'ssh-rsa': publicKey}
factory.portal = Portal(StandardRealm())

factory.portal.registerChecker(CVMChecker())

reactor.listenTCP(2022, factory)
reactor.run()
