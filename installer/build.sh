cd slave
echo "Copying needed files for slave SFX..."
cp ../../runhelper/runhelper .
cp ../../console/slave/dropper .
cp ../../logshell/logshell .
cp ../../logshell/cvmshell .
cp ../../logshell/logcmd .
cp ../common/setuplib.py .
echo "Creating slave SFX..."
tar -czf - * | python ../../tools/pysfx/pysfx.py -as "python install.py" - ../slave_sfx.py
echo "Removing copied files..."
rm runhelper
rm dropper
rm logshell
rm cvmshell
rm logcmd
rm setuplib.py
cd ..

cd master
echo "Copying needed files for master SFX..."
echo "Creating master SFX..."
tar -czf - * | python ../../tools/pysfx/pysfx.py -as "python install.py" - ../master_sfx.py
echo "Removing copied files..."
cd ..

echo "Done!"
